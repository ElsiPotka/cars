<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/brands', [App\Http\Controllers\Api\BrandController::class, 'index']);
    Route::get('/brands/{brand}', [App\Http\Controllers\Api\BrandController::class, 'show']);
    Route::get('/categories', [App\Http\Controllers\Api\CategoryController::class, 'index']);
    Route::get('/categories/{category}', [App\Http\Controllers\Api\CategoryController::class, 'show']);

    Route::middleware(['role:admin'])->group(function () {
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
    });
});
