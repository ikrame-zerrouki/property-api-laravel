<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ==================== Public Routes ====================
Route::get('/health', function () {
    return response()->json(['success' => true, 'message' => 'API is working']);
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/properties', [PropertyController::class, 'index']);
Route::get('/properties/{id}', [PropertyController::class, 'show']);

// ==================== Protected Routes ====================
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::prefix('properties')->group(function () {
        Route::post('/', [PropertyController::class, 'store']);
        Route::put('/{id}', [PropertyController::class, 'update']);
        Route::delete('/{id}', [PropertyController::class, 'destroy']);
        Route::patch('/{id}/toggle-publish', [PropertyController::class, 'togglePublish']);
        Route::post('/{propertyId}/images', [ImageController::class, 'upload']);
        Route::get('/{propertyId}/images', [ImageController::class, 'getPropertyImages']);
    });

    Route::prefix('images')->group(function () {
        Route::delete('/{id}', [ImageController::class, 'destroy']);
        Route::post('/bulk-delete', [ImageController::class, 'bulkDelete']);
        Route::patch('/{id}/set-primary', [ImageController::class, 'setPrimary']);
    });
});

// ✅ Test route تم حذفه بالكامل - أكثر أماناً
