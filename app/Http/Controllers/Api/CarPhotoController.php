<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BulkStoreCarPhotoRequest;
use App\Http\Requests\StoreCarPhotoRequest;
use App\Http\Requests\UpdateCarPhotoRequest;
use App\Http\Resources\CarPhotoResource;
use App\Models\CarPhoto;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CarPhotoController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCarPhotoRequest $request): CarPhotoResource|JsonResponse
    {
        try {
            return DB::transaction(function () use ($request) {
                $validated = $request->validated();
                $carId = $validated['car_id'];

                $file = $request->file('photo');
                $path = $this->handlePhotoUpload($file, $carId);

                $photo = CarPhoto::create([
                    'car_id' => $carId,
                    'path' => $path,
                ]);

                return new CarPhotoResource($photo);
            });
        } catch (\Throwable $e) {
            Log::error('Error storing car photo: '.$e->getMessage());

            return response()->json(['message' => 'Something went wrong, please try again later.'], 500);
        }
    }

    /**
     * Store multiple resources in storage.
     */
    public function bulkStore(BulkStoreCarPhotoRequest $request): JsonResponse
    {
        try {
            return DB::transaction(function () use ($request) {
                $validated = $request->validated();
                $carId = $validated['car_id'];
                $photos = [];

                if ($request->hasFile('photos')) {
                    foreach ($request->file('photos') as $file) {
                        $path = $this->handlePhotoUpload($file, $carId);
                        $photo = CarPhoto::create([
                            'car_id' => $carId,
                            'path' => $path,
                        ]);
                        $photos[] = $photo;
                    }
                }

                return response()->json([
                    'data' => CarPhotoResource::collection($photos),
                    'message' => 'Photos uploaded successfully',
                ], 201);
            });
        } catch (\Throwable $e) {
            Log::error('Error bulk storing car photos: '.$e->getMessage());

            return response()->json(['message' => 'Something went wrong, please try again later.'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CarPhoto $carPhoto): CarPhotoResource
    {
        return new CarPhotoResource($carPhoto);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCarPhotoRequest $request, CarPhoto $carPhoto): CarPhotoResource|JsonResponse
    {
        try {
            return DB::transaction(function () use ($request, $carPhoto) {
                $file = $request->file('photo');

                if (Storage::disk('public')->exists($carPhoto->path)) {
                    Storage::disk('public')->delete($carPhoto->path);
                }

                $path = $this->handlePhotoUpload($file, $carPhoto->car_id);

                $carPhoto->update(['path' => $path]);

                return new CarPhotoResource($carPhoto);
            });
        } catch (\Throwable $e) {
            Log::error('Error updating car photo: '.$e->getMessage());

            return response()->json(['message' => 'Something went wrong, please try again later.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CarPhoto $carPhoto): JsonResponse
    {
        $carPhoto->delete();

        return response()->json(['message' => 'Photo deleted successfully']);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(string $id): CarPhotoResource
    {
        $carPhoto = CarPhoto::onlyTrashed()->findOrFail($id);
        $carPhoto->restore();

        return new CarPhotoResource($carPhoto);
    }

    /**
     * Force remove the specified resource from storage.
     */
    public function forceDelete(string $id): JsonResponse
    {
        $carPhoto = CarPhoto::withTrashed()->findOrFail($id);

        if (Storage::disk('public')->exists($carPhoto->path)) {
            Storage::disk('public')->delete($carPhoto->path);
        }

        $carPhoto->forceDelete();

        return response()->json(['message' => 'Photo permanently deleted']);
    }

    private function handlePhotoUpload($file, string $carId): string
    {
        $filename = \Illuminate\Support\Str::random(40).'.'.$file->getClientOriginalExtension();
        $path = "cars/{$carId}/{$filename}";

        Storage::disk('public')->putFileAs("cars/{$carId}", $file, $filename);

        return $path;
    }
}
