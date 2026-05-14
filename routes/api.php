<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    CategoryController,
    ContactController,
    OrderController,
    PaymentController,
    ProductController,
    ReviewController,
    SearchController,
    WalletController,
    CouponController
};

/*
|--------------------------------------------------------------------------
| Public Routes (No Login Required)
|--------------------------------------------------------------------------
*/

Route::get('/', function(){
    return response()->json(['message' => 'Welcome to the API']);
});

// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Products & Categories (Read Only)
// IMPORTANT: Define specific sub-routes BEFORE resource routes
Route::get('/products/paginate', [ProductController::class, 'paginate']); // <--- Your new Pagination Route
Route::get('/search', [SearchController::class, 'search']);
Route::apiResource('/products', ProductController::class)->only(['index', 'show']);
Route::apiResource('/categories', CategoryController::class)->only(['index', 'show']);

// Reviews (Read Only)
Route::get('/reviews', [ReviewController::class, 'index']);

// Contact
Route::post('/contact', [ContactController::class, 'send']);


/*
|--------------------------------------------------------------------------
| Protected Routes (Logged In Users)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    
    // User Profile
    Route::get('/user', function (Request $request) { return $request->user(); });
    Route::put('/update/profile', [AuthController::class, 'updateProfile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Orders
    Route::apiResource('/orders', OrderController::class);
    Route::put('/orders/{id}/cancel', [OrderController::class, 'cancel']);

    // Payments & Wallets
    Route::post('/pay', [PaymentController::class, 'initialize'])->name('api.payment.initialize');
    Route::get('/pay/callback', [PaymentController::class, 'callback'])->name('api.payment.callback');
    Route::get('/wallet', [WalletController::class, 'index']);
    
    // Reviews (Write)
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{id}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);
    Route::post('/user/review-status', [ReviewController::class, 'updateReviewStatus']);

    // Coupons (Validation)
    Route::post('/coupons/validate', [CouponController::class, 'validateCoupon']);
});


/*
|--------------------------------------------------------------------------
| Admin Routes (Middleware: auth + admin)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    
    // Admin: Manage Products & Categories
    Route::apiResource('/products', ProductController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('/categories', CategoryController::class)->only(['store', 'update', 'destroy']);

    // Admin: View All Orders
    Route::get('admin/orders', [OrderController::class, 'adminIndex']);

    // Admin: Coupons
    Route::apiResource('/coupons', CouponController::class);
});