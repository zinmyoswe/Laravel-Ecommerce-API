<?php 

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SubcategoryController;

Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']); // List all products
    Route::get('/{id}', [ProductController::class, 'show']); // Product detail
    Route::get('/category/{categoryId}', [ProductController::class, 'byCategory']); // Filter by category
    Route::get('/subcategory/{subcategoryId}', [ProductController::class, 'bySubcategory']); // Filter by subcategory
    Route::put('/{id}', [ProductController::class, 'update']);
    Route::put('/{id}/similar', [ProductController::class, 'updateSimilarProducts']);


});

Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']); // List categories
    Route::get('/{id}', [CategoryController::class, 'show']); // Category detail
    Route::post('categories', [CategoryController::class, 'store']);
    Route::put('categories/{id}', [CategoryController::class, 'update']);
    Route::delete('categories/{id}', [CategoryController::class, 'destroy']);
});

Route::prefix('subcategories')->group(function () {
    Route::get('/', [SubcategoryController::class, 'index']); // List subcategories
    Route::get('/{id}', [SubcategoryController::class, 'show']); // Subcategory detail
});

