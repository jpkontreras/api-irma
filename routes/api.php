<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\MenuItemController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\RestaurantController;
use App\Http\Controllers\Api\V1\StaffController;
use App\Http\Controllers\Api\V1\TableController;
use App\Http\Controllers\Api\V1\OrderItemController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Authentication routes
Route::post('auth/login', [AuthController::class, 'login']);
Route::get('ok', function () {
    return response()->json([
        'message' => 'API is running',
    ]);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);

    // Restaurant management
    Route::apiResource('restaurants', RestaurantController::class);

    // Staff management
    Route::post('staff/validate-pin', [StaffController::class, 'validatePin']);
    Route::apiResource('staff', StaffController::class);

    // Table management
    Route::patch('tables/{table}/status', [TableController::class, 'updateStatus']);
    Route::apiResource('tables', TableController::class);

    // Menu management
    Route::patch('menu-items/{menuItem}/availability', [MenuItemController::class, 'toggleAvailability']);
    Route::apiResource('menu-items', MenuItemController::class);

    // Order management
    Route::prefix('orders')->group(function () {
        Route::get('kitchen', [OrderController::class, 'kitchen']);
        Route::get('active', [OrderController::class, 'active']);
        Route::patch('{order}/status', [OrderController::class, 'updateStatus']);
        Route::patch('{order}/sync', [OrderController::class, 'sync']);
    });
    Route::apiResource('orders', OrderController::class);

    // Order items management (as nested resource)
    Route::apiResource('orders.items', OrderItemController::class)->except(['index']);
});
