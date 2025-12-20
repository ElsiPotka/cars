<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $search = strip_tags($request->input('search', ''));

        $query = Brand::query();

        if ($search) {

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('country', 'like', "%{$search}%");
            });
        }

        $brands = $query->orderBy('name')->paginate(15);

        return BrandResource::collection($brands);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBrandRequest $request): BrandResource|JsonResponse
    {
        try {
            return DB::transaction(function () use ($request) {
                $data = $request->validated();

                if ($request->hasFile('logo')) {
                    $data['logo_path'] = $this->handleLogoUpload($request->file('logo'), $data['name']);
                }

                $brand = Brand::create($data);

                return new BrandResource($brand);
            });
        } catch (\Throwable $e) {
            Log::error('Error storing brand: '.$e->getMessage());

            return response()->json(['message' => 'Something went wrong, please try again later.'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand): BrandResource
    {
        return new BrandResource($brand);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBrandRequest $request, Brand $brand): BrandResource|JsonResponse
    {
        try {
            return DB::transaction(function () use ($request, $brand) {
                $data = $request->validated();

                if ($request->hasFile('logo')) {
                    if ($brand->logo_path && Storage::disk('public')->exists($brand->logo_path)) {
                        Storage::disk('public')->delete($brand->logo_path);
                    }
                    $data['logo_path'] = $this->handleLogoUpload($request->file('logo'), $data['name'] ?? $brand->name);
                }

                $brand->update($data);

                return new BrandResource($brand);
            });
        } catch (\Throwable $e) {
            Log::error('Error updating brand: '.$e->getMessage());

            return response()->json(['message' => 'Something went wrong, please try again later.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand): JsonResponse
    {
        $brand->delete();

        return response()->json(['message' => 'Brand deleted successfully']);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(string $id): BrandResource
    {
        $brand = Brand::onlyTrashed()->findOrFail($id);
        $brand->restore();

        return new BrandResource($brand);
    }

    /**
     * Force remove the specified resource from storage.
     */
    public function forceDelete(string $id): JsonResponse
    {
        $brand = Brand::withTrashed()->findOrFail($id);

        if ($brand->logo_path && Storage::disk('public')->exists($brand->logo_path)) {
            Storage::disk('public')->delete($brand->logo_path);
        }

        $brand->forceDelete();

        return response()->json(['message' => 'Brand permanently deleted']);
    }

    private function handleLogoUpload($file, string $brandName): string
    {
        $slug = \Illuminate\Support\Str::slug($brandName);
        $extension = $file->getClientOriginalExtension();
        $filename = "{$slug}.{$extension}";

        $file->storeAs('brands', $filename, 'public');

        return "brands/{$filename}";
    }
}
