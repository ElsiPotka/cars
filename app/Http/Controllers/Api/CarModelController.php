<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCarModelRequest;
use App\Http\Requests\UpdateCarModelRequest;
use App\Http\Resources\CarModelResource;
use App\Models\CarModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

class CarModelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $search = strip_tags($request->input('search', ''));

        $query = CarModel::with('brand');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('year', 'like', "%{$search}%")
                    ->orWhereHas('brand', function ($brandQuery) use ($search) {
                        $brandQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $models = $query->orderBy('name')->paginate(15);

        return CarModelResource::collection($models);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCarModelRequest $request): CarModelResource|JsonResponse
    {
        try {
            $model = CarModel::create($request->validated());
            $model->load('brand');

            return new CarModelResource($model);
        } catch (\Throwable $e) {
            Log::error('Error storing car model: '.$e->getMessage());

            return response()->json(['message' => 'Something went wrong, please try again later.'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CarModel $carModel): CarModelResource
    {
        $carModel->load('brand');

        return new CarModelResource($carModel);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCarModelRequest $request, CarModel $carModel): CarModelResource|JsonResponse
    {
        try {
            $carModel->update($request->validated());
            $carModel->load('brand');

            return new CarModelResource($carModel);
        } catch (\Throwable $e) {
            Log::error('Error updating car model: '.$e->getMessage());

            return response()->json(['message' => 'Something went wrong, please try again later.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CarModel $carModel): JsonResponse
    {
        $carModel->delete();

        return response()->json(['message' => 'Car model deleted successfully']);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(string $id): CarModelResource
    {
        $model = CarModel::onlyTrashed()->findOrFail($id);
        $model->restore();
        $model->load('brand');

        return new CarModelResource($model);
    }

    /**
     * Force remove the specified resource from storage.
     */
    public function forceDelete(string $id): JsonResponse
    {
        $model = CarModel::withTrashed()->findOrFail($id);
        $model->forceDelete();

        return response()->json(['message' => 'Car model permanently deleted']);
    }
}
