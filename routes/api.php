<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\RoomController;


/// Global API Routes
Route::get('/owner', [OwnerController::class, 'index']);
Route::post('/owner/register', [OwnerController::class, 'register']);
Route::post('/owner/login', [OwnerController::class, 'login']);

/// Protected API Routes
Route::middleware('auth:sanctum')->group(function () {
    /// Owner-specific routes
    Route::prefix('owner')->group(function () {
        Route::get('/profile', [OwnerController::class, 'profile']);
        Route::get('/rooms', [OwnerController::class, 'rooms']);
        Route::post('/profile', [OwnerController::class, 'updateProfile']);
        Route::post('/logout/current', [OwnerController::class, 'logoutCurrentDevice']);
        Route::post('/logout/all', [OwnerController::class, 'logoutAllDevices']);
    });

    /// Room-specific routes
    Route::prefix('rooms')->group(function () {
        Route::get('/', [RoomController::class, 'index']);
        Route::post('/', [RoomController::class, 'store']);
        Route::put('/{id}', [RoomController::class, 'update']);
        Route::delete('/{id}', [RoomController::class, 'destroy']);
    });
});
