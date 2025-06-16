<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SubcategoryController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StripeWebhookController;

// ✅ Product Routes
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/filter', [ProductController::class, 'filter']);
    Route::get('/{id}', [ProductController::class, 'show']);
    Route::get('/category/{categoryId}', [ProductController::class, 'byCategory']);
    Route::get('/subcategory/{subcategoryId}', [ProductController::class, 'bySubcategory']);
    Route::put('/{id}', [ProductController::class, 'update']);
    Route::put('/{id}/similar', [ProductController::class, 'updateSimilarProducts']);
});

// ✅ Category Routes
Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/{id}', [CategoryController::class, 'show']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::put('/{id}', [CategoryController::class, 'update']);
    Route::delete('/{id}', [CategoryController::class, 'destroy']);
});

// ✅ Subcategory Routes
Route::prefix('subcategories')->group(function () {
    Route::get('/', [SubcategoryController::class, 'index']);
    Route::get('/{id}', [SubcategoryController::class, 'show']);
    Route::post('/', [SubcategoryController::class, 'store']);
    Route::put('/{id}', [SubcategoryController::class, 'update']);
    Route::delete('/{id}', [SubcategoryController::class, 'destroy']);
});

// ✅ Guest Cart (Session-based)
Route::get('/cart/session/{session_id}', [CartController::class, 'showBySession']);
Route::post('/cart/session-store', [CartController::class, 'storeForGuest']);

// ✅ Authenticated Cart (User-based)
Route::middleware('auth:sanctum')->prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index']);
    Route::post('/', [CartController::class, 'store']);
    Route::put('/{product_id}', [CartController::class, 'update']);
    Route::delete('/{product_id}', [CartController::class, 'destroy']);
});

// ✅ Checkout & Orders
Route::post('/checkout', [OrderController::class, 'store']);
Route::get('/orders/guest/{session_id}', [OrderController::class, 'guestOrders']);
Route::get('/orders/{id}', [OrderController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/user/{user_id}', [OrderController::class, 'getUserOrders']);

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// ✅ Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ✅ Payment
Route::post('/payment/charge', [PaymentController::class, 'charge']);
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);
