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
});
