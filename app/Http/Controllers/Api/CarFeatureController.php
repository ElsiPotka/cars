<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCarFeatureRequest;
use App\Http\Requests\UpdateCarFeatureRequest;
use App\Http\Resources\CarFeatureResource;
use App\Models\CarFeature;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

class CarFeatureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $search = strip_tags($request->input('search', ''));

        $query = CarFeature::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        $features = $query->orderBy('name')->paginate(15);

        return CarFeatureResource::collection($features);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCarFeatureRequest $request): CarFeatureResource|JsonResponse
    {
        try {
            $feature = CarFeature::create($request->validated());

            return new CarFeatureResource($feature);
        } catch (\Throwable $e) {
            Log::error('Error storing car feature: '.$e->getMessage());

            return response()->json(['message' => 'Something went wrong, please try again later.'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CarFeature $carFeature): CarFeatureResource
    {
        return new CarFeatureResource($carFeature);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCarFeatureRequest $request, CarFeature $carFeature): CarFeatureResource|JsonResponse
    {
        try {
            $carFeature->update($request->validated());

            return new CarFeatureResource($carFeature);
        } catch (\Throwable $e) {
            Log::error('Error updating car feature: '.$e->getMessage());

            return response()->json(['message' => 'Something went wrong, please try again later.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CarFeature $carFeature): JsonResponse
    {
        $carFeature->delete();

        return response()->json(['message' => 'Car feature deleted successfully']);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(string $id): CarFeatureResource
    {
        $feature = CarFeature::onlyTrashed()->findOrFail($id);
        $feature->restore();

        return new CarFeatureResource($feature);
    }

    /**
     * Force remove the specified resource from storage.
     */
    public function forceDelete(string $id): JsonResponse
    {
        $feature = CarFeature::withTrashed()->findOrFail($id);
        $feature->forceDelete();

        return response()->json(['message' => 'Car feature permanently deleted']);
    }
}
