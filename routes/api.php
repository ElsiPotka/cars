<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public routes with rate limiting for browsing catalog
Route::middleware(['throttle:api'])->group(function () {
    Route::get('/brands', [App\Http\Controllers\Api\BrandController::class, 'index']);
    Route::get('/brands/{brand}', [App\Http\Controllers\Api\BrandController::class, 'show']);
    Route::get('/categories', [App\Http\Controllers\Api\CategoryController::class, 'index']);
    Route::get('/categories/{category}', [App\Http\Controllers\Api\CategoryController::class, 'show']);
    Route::get('/car-features', [App\Http\Controllers\Api\CarFeatureController::class, 'index']);
    Route::get('/car-features/{feature}', [App\Http\Controllers\Api\CarFeatureController::class, 'show']);
    Route::get('/car-models', [App\Http\Controllers\Api\CarModelController::class, 'index']);
    Route::get('/car-models/{carModel}', [App\Http\Controllers\Api\CarModelController::class, 'show']);
    Route::get('/cars', [App\Http\Controllers\Api\CarController::class, 'index']);
    Route::get('/cars/{car}', [App\Http\Controllers\Api\CarController::class, 'show']);
    Route::get('/car-photos/{carPhoto}', [App\Http\Controllers\Api\CarPhotoController::class, 'show']);
});

// Authenticated + Admin routes for write operations
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::post('/brands', [App\Http\Controllers\Api\BrandController::class, 'store']);
    Route::put('/brands/{brand}', [App\Http\Controllers\Api\BrandController::class, 'update']);
    Route::delete('/brands/{brand}', [App\Http\Controllers\Api\BrandController::class, 'destroy']);
    Route::post('/brands/{id}/restore', [App\Http\Controllers\Api\BrandController::class, 'restore']);
    Route::delete('/brands/{id}/force', [App\Http\Controllers\Api\BrandController::class, 'forceDelete']);

    Route::post('/categories', [App\Http\Controllers\Api\CategoryController::class, 'store']);
    Route::put('/categories/{category}', [App\Http\Controllers\Api\CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [App\Http\Controllers\Api\CategoryController::class, 'destroy']);
    Route::post('/categories/{id}/restore', [App\Http\Controllers\Api\CategoryController::class, 'restore']);
    Route::delete('/categories/{id}/force', [App\Http\Controllers\Api\CategoryController::class, 'forceDelete']);

    Route::post('/car-features', [App\Http\Controllers\Api\CarFeatureController::class, 'store']);
    Route::put('/car-features/{carFeature}', [App\Http\Controllers\Api\CarFeatureController::class, 'update']);
    Route::delete('/car-features/{carFeature}', [App\Http\Controllers\Api\CarFeatureController::class, 'destroy']);
    Route::post('/car-features/{id}/restore', [App\Http\Controllers\Api\CarFeatureController::class, 'restore']);
    Route::delete('/car-features/{id}/force', [App\Http\Controllers\Api\CarFeatureController::class, 'forceDelete']);

    Route::post('/car-models', [App\Http\Controllers\Api\CarModelController::class, 'store']);
    Route::put('/car-models/{carModel}', [App\Http\Controllers\Api\CarModelController::class, 'update']);
    Route::delete('/car-models/{carModel}', [App\Http\Controllers\Api\CarModelController::class, 'destroy']);
    Route::post('/car-models/{id}/restore', [App\Http\Controllers\Api\CarModelController::class, 'restore']);
    Route::delete('/car-models/{id}/force', [App\Http\Controllers\Api\CarModelController::class, 'forceDelete']);

    Route::post('/cars', [App\Http\Controllers\Api\CarController::class, 'store']);
    Route::put('/cars/{car}', [App\Http\Controllers\Api\CarController::class, 'update']);
    Route::delete('/cars/{car}', [App\Http\Controllers\Api\CarController::class, 'destroy']);
    Route::post('/cars/{id}/restore', [App\Http\Controllers\Api\CarController::class, 'restore']);
    Route::delete('/cars/{id}/force', [App\Http\Controllers\Api\CarController::class, 'forceDelete']);


    Route::post('/car-photos', [App\Http\Controllers\Api\CarPhotoController::class, 'store']);
    Route::post('/car-photos/bulk', [App\Http\Controllers\Api\CarPhotoController::class, 'bulkStore']);
    Route::put('/car-photos/{carPhoto}', [App\Http\Controllers\Api\CarPhotoController::class, 'update']);
    Route::delete('/car-photos/{carPhoto}', [App\Http\Controllers\Api\CarPhotoController::class, 'destroy']);
    Route::post('/car-photos/{id}/restore', [App\Http\Controllers\Api\CarPhotoController::class, 'restore']);
    Route::delete('/car-photos/{id}/force', [App\Http\Controllers\Api\CarPhotoController::class, 'forceDelete']);
});

// Authenticated + Admin or Manager routes
Route::middleware(['auth:sanctum', 'role:admin|manager'])->group(function () {
    Route::post('/companies', [App\Http\Controllers\Api\CompanyController::class, 'store']);
    Route::get('/companies/{company}', [App\Http\Controllers\Api\CompanyController::class, 'show']);
    Route::put('/companies/{company}', [App\Http\Controllers\Api\CompanyController::class, 'update']);
    Route::delete('/companies/{company}', [App\Http\Controllers\Api\CompanyController::class, 'destroy']);
    
    Route::get('/companies/{company}/employees', [App\Http\Controllers\Api\CompanyController::class, 'getEmployees']);
    Route::post('/companies/{company}/employees', [App\Http\Controllers\Api\CompanyController::class, 'addEmployee']);
    Route::put('/companies/{company}/employees/{employee}', [App\Http\Controllers\Api\CompanyController::class, 'updateEmployee']);
    Route::delete('/companies/{company}/employees/{employee}', [App\Http\Controllers\Api\CompanyController::class, 'removeEmployee']);
    
    Route::get('/companies/{company}/cars', [App\Http\Controllers\Api\CarController::class, 'companyIndex']);
});
