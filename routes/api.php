<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — v1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // ─── Public ───────────────────────────────────────────────────────────
    Route::post('login',    [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);

    // ─── Authenticated ────────────────────────────────────────────────────
    Route::middleware('auth:api')->group(function () {

        // Logout
        Route::post('logout', [AuthController::class, 'logout']);

        // Current user profile
        Route::prefix('profile')->group(function () {
            Route::get('/',          [UserController::class, 'user']);
            Route::put('/info',      [UserController::class, 'updateInfo']);
            Route::put('/password',  [UserController::class, 'updatePassword']);
        });

        // ─── Admin only ───────────────────────────────────────────────────
        Route::middleware('role:Admin')->group(function () {
            Route::apiResource('users', UserController::class);
            Route::apiResource('roles', RoleController::class);
        });

        // ─── Admin + editor ───────────────────────────────────────────────
        Route::middleware('role:Admin,Editor')->group(function () {
            Route::apiResource('products', ProductController::class);
            Route::post('uploads', [ImageController::class, 'upload']);
        });

        // ─── Any authenticated user ───────────────────────────────────────
        Route::apiResource('orders', OrderController::class)->only(['index', 'show']);
        Route::get('orders/export', [OrderController::class, 'export']);
    });
});
