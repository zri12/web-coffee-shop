<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes - Cafe Web Ordering System
|--------------------------------------------------------------------------
|
| Public routes - No authentication required
| Protected routes - Require Sanctum token
| Admin routes - Require admin or manager role
|
*/

// ===========================================
// PUBLIC ROUTES (No Auth Required)
// ===========================================

// Categories
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);

// Menus
Route::get('/menus', [MenuController::class, 'index']);
Route::get('/menus/{menu}', [MenuController::class, 'show']);

// Orders (Create & Track)
Route::post('/orders', [OrderController::class, 'store']);
Route::post('/orders/track', [OrderController::class, 'track']);

// ===========================================
// AUTH ROUTES
// ===========================================

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    
    // Protected auth routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
    });
});

// ===========================================
// PROTECTED ROUTES (Require Auth)
// ===========================================

Route::middleware('auth:sanctum')->group(function () {
    
    // -----------------------------------------
    // CASHIER & ADMIN ROUTES
    // -----------------------------------------
    
    // Orders Management
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus']);
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);
    
    // -----------------------------------------
    // ADMIN & MANAGER ONLY ROUTES
    // -----------------------------------------
    
    Route::middleware('role:admin,manager')->group(function () {
        
        // Dashboard & Reports
        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::get('/reports', [DashboardController::class, 'reports']);
        
        // Category Management
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{category}', [CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
        
        // Menu Management
        Route::post('/menus', [MenuController::class, 'store']);
        Route::put('/menus/{menu}', [MenuController::class, 'update']);
        Route::patch('/menus/{menu}/toggle', [MenuController::class, 'toggleAvailability']);
        Route::delete('/menus/{menu}', [MenuController::class, 'destroy']);
        
        // User Management
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{user}', [UserController::class, 'show']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
    });
});

// ===========================================
// HEALTH CHECK
// ===========================================

Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is running',
        'version' => '1.0.0',
        'timestamp' => now()->toIso8601String(),
    ]);
});
