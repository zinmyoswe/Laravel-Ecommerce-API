<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // List all products
    public function index()
    {
        $products = Product::with(['category', 'subcategory', 'similarProducts'])->get();
        return response()->json($products);
    }

    // Product detail
    public function show($id)
    {
        $product = Product::with(['category', 'subcategory', 'similarProducts', 'sizes'])->findOrFail($id);
        return response()->json($product);
    }

    // Filter by category
    public function byCategory($categoryId)
    {
        $products = Product::where('category_id', $categoryId)->with(['category', 'subcategory'])->get();
        return response()->json($products);
    }

    // Filter by subcategory
    public function bySubcategory($subcategoryId)
    {
        $products = Product::where('subcategory_id', $subcategoryId)->with(['category', 'subcategory'])->get();
        return response()->json($products);
    }


    public function update(Request $request, $id)
{
    $product = Product::find($id);

    if (!$product) {
        return response()->json(['message' => 'Product not found'], 404);
    }

    $validated = $request->validate([
        'productname' => 'nullable|string',
        'productimage' => 'nullable|url',
        'productimages' => 'nullable|array',
        'productimages.*' => 'url',
        'productvideo' => 'nullable|url',
        'category_id' => 'nullable|exists:categories,categoryid',
        'subcategory_id' => 'nullable|exists:subcategories,subcategoryid',
        'color' => 'nullable|string',
        'price' => 'nullable|numeric',
        'discount' => 'nullable|numeric',
        'stock' => 'nullable|integer',
        'description' => 'nullable|string',
        'gender' => 'nullable|string',
        'adminid' => 'nullable|integer',
        'sameproductid' => 'nullable|exists:products,productid',
        'size_ids' => 'nullable|array',
        'size_ids.*' => 'exists:sizes,id',
    ]);

    $product->update($validated);

    // Sync sizes if provided
    if ($request->has('size_ids')) {
        $product->sizes()->sync($validated['size_ids']);
    }

    return response()->json([
        'message' => 'Product updated successfully',
        'product' => $product->load('sizes')
    ]);
}

    public function updateSimilarProducts(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $validated = $request->validate([
            'similar_product_ids' => 'required|array',
            'similar_product_ids.*' => 'exists:products,productid',
        ]);

        // Sync similar products
        $product->similarProducts()->sync($validated['similar_product_ids']);

        return response()->json([
            'message' => 'Similar products updated successfully',
            'similar_products' => $product->similarProducts()->get()
        ]);
    }
}
