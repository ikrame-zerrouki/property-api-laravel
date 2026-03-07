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

// ==================== Public Routes (no authentication required) ====================

// Health check route
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is working',
        'timestamp' => now()
    ]);
});

// Authentication routes (public)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']); // Optional

// Property routes - public (view only)
Route::get('/properties', [PropertyController::class, 'index']);
Route::get('/properties/{id}', [PropertyController::class, 'show']);

// ==================== Protected Routes (require authentication) ====================

Route::middleware('auth:sanctum')->group(function () {

    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Property routes - require authentication
    Route::prefix('properties')->group(function () {
        Route::post('/', [PropertyController::class, 'store']);
        Route::put('/{id}', [PropertyController::class, 'update']);
        Route::delete('/{id}', [PropertyController::class, 'destroy']);
        Route::patch('/{id}/toggle-publish', [PropertyController::class, 'togglePublish']);

        // Image routes (nested under property)
        Route::post('/{propertyId}/images', [ImageController::class, 'upload']);
        Route::get('/{propertyId}/images', [ImageController::class, 'getPropertyImages']);
    });

    // Separate image routes
    Route::prefix('images')->group(function () {
        Route::delete('/{id}', [ImageController::class, 'destroy']);
        Route::post('/bulk-delete', [ImageController::class, 'bulkDelete']);
        Route::patch('/{id}/set-primary', [ImageController::class, 'setPrimary']);
    });
});

// Test route (for debugging only)
Route::get('/test-delete/{id}', function($id) {
    try {
        $image = App\Models\Image::find($id);
        if (!$image) {
            return response()->json(['error' => 'Image not found'], 404);
        }

        \Storage::disk('public')->delete($image->path);
        $image->delete();

        return response()->json(['success' => true, 'message' => 'Test delete successful']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});
