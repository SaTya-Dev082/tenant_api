<?php

use App\Http\Controllers\MonthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\PaymentPeriodsController;
use App\Http\Controllers\RoomPhotoController;
use App\Http\Controllers\YearModelController;
use App\Http\Controllers\PaymentModelController;


/// Global API Routes
Route::get('/owner', [OwnerController::class, 'index']);
Route::post('/owner/register', [OwnerController::class, 'register']);
Route::post('/owner/login', [OwnerController::class, 'login']);

/// Protected API Routes
Route::middleware('auth:sanctum')->group(function () {
    /// Owner-specific routes
    Route::prefix('owner')->group(function () {
        Route::get('/profile', [OwnerController::class, 'profile']);
        Route::post('/profile', [OwnerController::class, 'updateProfile']);
        Route::post('/logout/current', [OwnerController::class, 'logoutCurrentDevice']);
        Route::post('/logout/all', [OwnerController::class, 'logoutAllDevices']);
    });

    /// Room-specific routes
    Route::prefix('rooms')->group(function () {
        Route::get('/', [RoomController::class, 'index']);
        Route::get('/sort-by-price', [RoomController::class, 'sortByPrice']);
        Route::get('/by-owner', [RoomController::class, 'getByOwner']);
        Route::get('/{id}', [RoomController::class, 'showPhotos']);
        Route::get('/{id}/details', [RoomController::class, 'showRoom']);
        Route::post('/', [RoomController::class, 'store']);
        Route::post('/{id}', [RoomController::class, 'update']);
        Route::delete('/{id}', [RoomController::class, 'destroy']);
    });

    /// Property-specific routes
    Route::prefix('/property')->group(function () {
        Route::get('/', [PropertyController::class, 'index']);
        Route::get('/{id}', [PropertyController::class, 'show']);
        Route::get('/room/{roomId}', [PropertyController::class, 'getByRoom']);
        Route::post('/{roomId}', [PropertyController::class, 'store']);
    });

    /// Tenant-specific routes
    Route::prefix('tenants')->group(function () {
        Route::get('/', [TenantController::class, 'index']);
        Route::get('/{id}', [TenantController::class, 'show']);
        Route::get('/by-owner', [TenantController::class, 'getByOwner']);
        Route::get('/{tenantId}/payments', [TenantController::class, 'payments']);
        Route::post('/', [TenantController::class, 'store']);
        Route::post('/{id}', [TenantController::class, 'update']);
        Route::delete('/{id}', [TenantController::class, 'destroy']);
    });

    /// Test-Payment-specific routes
    Route::prefix('payments-model')->group(function () {
        Route::get('/', [PaymentModelController::class, 'index']);
        // Route::get('/tenant/{tenant_id}', [PaymentModelController::class, 'getByTenant']);
        Route::get('/tenant/{tenantId}', [PaymentModelController::class, 'byTenant']);
        Route::post('/', [PaymentModelController::class, 'store']);
        Route::get('/sort-by-month-year/{month_id}/{year_id}', [PaymentModelController::class, 'sortByMonthYear']);
    });
    // Route month
    Route::get('/months', [MonthController::class, 'index']);
    Route::get('/years', [YearModelController::class, 'index']);
});

Route::get('/rooms', [RoomController::class, 'index']);
