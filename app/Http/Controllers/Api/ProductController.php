<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // List all products
    
    public function index(Request $request)
{
    $query = Product::with(['category', 'subcategory', 'similarProducts', 'sizes']);

    // ✅ Filter by subcategory
    if ($request->filled('subcategory_id')) {
        $query->where('subcategory_id', $request->input('subcategory_id'));
    }


    // ✅ Filter by gender (multi-select)
    if ($request->filled('gender')) {
        $genders = is_array($request->gender) ? $request->gender : [$request->gender];
        $query->whereIn('gender', $genders);
    }

    

    if ($request->has('color')) {
    $colors = $request->input('color');

    if (!is_array($colors)) {
        $colors = [$colors];
    }

    $query->whereIn('color', $colors); // ✅ supports multiple color filters
}


    // ✅ Filter by clothing or shoe sizes
    if ($request->filled('sizevalue')) {
        $sizeValues = is_array($request->sizevalue) ? $request->sizevalue : [$request->sizevalue];
        $query->whereHas('sizes', function ($q) use ($sizeValues) {
            $q->whereIn('sizevalue', $sizeValues);
        });
    }

    // ✅ Filter by price range shortcut values (e.g. "under_50")
    if ($request->filled('price')) {
        $priceRanges = is_array($request->price) ? $request->price : [$request->price];
        $query->where(function ($q) use ($priceRanges) {
            foreach ($priceRanges as $range) {
                switch ($range) {
                    case 'under_50':
                        $q->orWhere('price', '<', 50);
                        break;
                    case '50_100':
                        $q->orWhereBetween('price', [50, 100]);
                        break;
                    case '101_199':
                        $q->orWhereBetween('price', [101, 199]);
                        break;
                    case 'over_200':
                        $q->orWhere('price', '>', 200);
                        break;
                }
            }
        });
    }

    // ✅ Sorting
    if ($request->filled('sort')) {
        switch ($request->sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
        }
    }

    // ✅ Fallback to latest by productid if no sort specified
    if (!$request->filled('sort')) {
        $query->orderBy('productid', 'desc');
    }

    // ✅ Limit for thumbnail slider or other use cases
    if ($request->filled('limit')) {
        $query->limit((int) $request->input('limit'));
    }

        return response()->json($query->get());
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
