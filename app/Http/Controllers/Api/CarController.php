<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CarResource;
use App\Models\Car;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class CarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $search = strip_tags($request->input('search', ''));

        $query = Car::query()
            ->with(['carModel.brand', 'category', 'features', 'photos']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('carModel', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhereHas('brand', function ($q) use ($search) {
                                $q->where('name', 'like', "%{$search}%");
                            });
                    });
            });
        }

        $cars = $query->latest()->paginate(15);

        return CarResource::collection($cars);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(\App\Http\Requests\StoreCarRequest $request): CarResource|JsonResponse
    {
        try {
            return \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
                $company = $this->authorizeCompanyAccess($request);

                $validated = $request->validated();
                $carData = \Illuminate\Support\Arr::except($validated, ['features', 'photos']);
                $carData['company_id'] = $company->id;

                $car = Car::create($carData);

                if (isset($validated['features'])) {
                    $car->features()->sync($validated['features']);
                }

                if ($request->hasFile('photos')) {
                    foreach ($request->file('photos') as $file) {
                        $this->handlePhotoUpload($file, $car);
                    }
                }

                $car->load(['carModel.brand', 'category', 'features', 'photos']);

                return new CarResource($car);
            });
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Error storing car: '.$e->getMessage());

            return response()->json(['message' => 'Something went wrong, please try again later.'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Car $car): CarResource
    {
        $car->load(['carModel.brand', 'category', 'features', 'photos']);

        return new CarResource($car);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(\App\Http\Requests\UpdateCarRequest $request, Car $car): CarResource|JsonResponse
    {
        try {
            return \Illuminate\Support\Facades\DB::transaction(function () use ($request, $car) {
                $this->authorizeCompanyAccess($request, $car);

                $validated = $request->validated();

                $carData = \Illuminate\Support\Arr::except($validated, ['features', 'photos', 'deleted_photos']);
                unset($carData['company_id']);

                $car->update($carData);

                if (isset($validated['features'])) {
                    $car->features()->sync($validated['features']);
                }

                if (isset($validated['deleted_photos'])) {
                    foreach ($validated['deleted_photos'] as $photoId) {
                        $photo = \App\Models\CarPhoto::find($photoId);
                        if ($photo && $photo->car_id === $car->id) {
                            if (Storage::disk('public')->exists($photo->path)) {
                                Storage::disk('public')->delete($photo->path);
                            }
                            $photo->delete();
                        }
                    }
                }

                if ($request->hasFile('photos')) {
                    foreach ($request->file('photos') as $file) {
                        $this->handlePhotoUpload($file, $car);
                    }
                }

                $car->load(['carModel.brand', 'category', 'features', 'photos']);

                return new CarResource($car);
            });
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Error updating car: '.$e->getMessage());

            return response()->json(['message' => 'Something went wrong, please try again later.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(Request $request, Car $car): JsonResponse
    {
        $this->authorizeCompanyAccess($request, $car);

        $car->delete();

        return response()->json(['message' => 'Car deleted successfully']);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(string $id): CarResource
    {
        $car = Car::onlyTrashed()->findOrFail($id);
        $car->restore();

        $car->load(['carModel.brand', 'category', 'features', 'photos']);

        return new CarResource($car);
    }

    /**
     * Force remove the specified resource from storage.
     */
    public function forceDelete(string $id): JsonResponse
    {
        $car = Car::withTrashed()->findOrFail($id);

        $directory = "cars/{$car->id}";
        if (Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->deleteDirectory($directory);
        }

        $car->forceDelete();

        return response()->json(['message' => 'Car permanently deleted']);
    }

    private function authorizeCompanyAccess(Request $request, ?Car $car = null): ?\App\Models\Company
    {
        $user = $request->user();
        $company = $user->company;

        if (!$company) {
             abort(403, 'User does not belong to a company.');
        }

        if (!$user->hasRole(['admin', 'manager'])) {
            abort(403, 'Unauthorized.');
        }

        if ($car && $car->company_id !== $company->id) {
            abort(403, 'Unauthorized access to this car.');
        }

        return $company;
    }

    /**
     * Display a listing of the resource for the company.
     */
    public function companyIndex(Request $request, \App\Models\Company $company): AnonymousResourceCollection
    {
         // Ensure the user belongs to this company and has permission
         $user = $request->user();
         if (!$user->hasRole(['admin', 'manager']) || $user->company?->id !== $company->id) {
             abort(403, 'Unauthorized.');
         }
 
         $search = strip_tags($request->input('search', ''));
 
         $query = $company->cars()
             ->with(['carModel.brand', 'category', 'features', 'photos']);
 
         if ($search) {
             $query->where(function ($q) use ($search) {
                 $q->where('name', 'like', "%{$search}%")
                     ->orWhere('description', 'like', "%{$search}%")
                     ->orWhereHas('carModel', function ($q) use ($search) {
                         $q->where('name', 'like', "%{$search}%")
                             ->orWhereHas('brand', function ($q) use ($search) {
                                 $q->where('name', 'like', "%{$search}%");
                             });
                     });
             });
         }
 
         $cars = $query->latest()->paginate(15);
 
         return CarResource::collection($cars);
    }
}
