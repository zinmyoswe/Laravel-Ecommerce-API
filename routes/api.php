<?php 

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SubcategoryController;
use App\Http\Controllers\Api\CartController;

Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']); // List all products
    Route::get('/filter', [ProductController::class, 'filter']);
    Route::get('/{id}', [ProductController::class, 'show']); // Product detail
    Route::get('/category/{categoryId}', [ProductController::class, 'byCategory']); // Filter by category
    Route::get('/subcategory/{subcategoryId}', [ProductController::class, 'bySubcategory']); // Filter by subcategory
    Route::put('/{id}', [ProductController::class, 'update']);
    Route::put('/{id}/similar', [ProductController::class, 'updateSimilarProducts']);



});

// Categories
Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']); // List categories
    Route::get('/{id}', [CategoryController::class, 'show']); // Category detail
    Route::post('/', [CategoryController::class, 'store']); // ✅ fixed path
    Route::put('/{id}', [CategoryController::class, 'update']); // ✅ fixed path
    Route::delete('/{id}', [CategoryController::class, 'destroy']); // ✅ fixed path
});

// Subcategories
Route::prefix('subcategories')->group(function () {
    Route::get('/', [SubcategoryController::class, 'index']); // List subcategories
    Route::get('/{id}', [SubcategoryController::class, 'show']); // Subcategory detail
    Route::post('/', [SubcategoryController::class, 'store']); // ✅ fixed path
    Route::put('/{id}', [SubcategoryController::class, 'update']); // ✅ fixed path
    Route::delete('/{id}', [SubcategoryController::class, 'destroy']); // ✅ fixed path
});


// ✅ Cart Routes
Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index']);
    Route::post('/', [CartController::class, 'store']);
    Route::put('/{product_id}', [CartController::class, 'update']);
    Route::delete('/{product_id}', [CartController::class, 'destroy']);
    Route::get('/session/{session_id}', [CartController::class, 'showBySession']);
});

