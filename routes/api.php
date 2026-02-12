<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\PaymentPeriodsController;
use App\Http\Controllers\RoomPhotoController;


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
        Route::get('/by-owner', [RoomController::class, 'getByOwner']);
        Route::get('/{id}', [RoomController::class, 'show']);
        Route::post('/', [RoomController::class, 'store']);
        Route::post('/{id}', [RoomController::class, 'update']);
        Route::delete('/{id}', [RoomController::class, 'destroy']);
    });

    /// Property-specific routes
    Route::prefix('/property')->group(function () {
        Route::get('/', [PropertyController::class, 'index']);
        Route::post('/{roomId}', [PropertyController::class, 'store']);
        Route::post('/{id}', [PropertyController::class, 'update']);
        Route::post('/{id}/toggle-moto-parking', [PropertyController::class, 'toggleMotoParking']);
    });

    /// Tenant-specific routes
    Route::prefix('tenants')->group(function () {
        Route::get('/', [TenantController::class, 'index']);
        Route::get('/by-owner', [TenantController::class, 'getByOwner']);
        Route::get('/{tenantId}/payments', [TenantController::class, 'payments']);
        Route::get('/{tenantId}/payment-periods', [TenantController::class, 'paymentPeriodsByTenant']);
        Route::post('/', [TenantController::class, 'store']);
        Route::post('/{id}', [TenantController::class, 'update']);
        Route::delete('/{id}', [TenantController::class, 'destroy']);
    });

    /// Payment-specific routes
    Route::prefix('payments')->group(function () {
        Route::get('/', [PaymentController::class, 'index']);
        Route::get('/by-tenant/{tenantId}', [PaymentController::class, 'getPaymentsByTenant']);
        Route::post('/', [PaymentController::class, 'store']);
    });
});
