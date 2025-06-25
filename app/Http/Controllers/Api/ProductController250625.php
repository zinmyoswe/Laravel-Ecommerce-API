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


    // filter Low-High, High-low, newest
    // public function filter(Request $request)
    //     {
    //         $sort = $request->query('sort');

    //         $query = Product::query();

    //         if ($sort === 'price_asc') {
    //             $query->orderBy('price', 'asc');
    //         } elseif ($sort === 'price_desc') {
    //             $query->orderBy('price', 'desc');
    //         } elseif ($sort === 'newest') {
    //             $query->orderBy('created_at', 'desc');
    //         }

    //         return response()->json(
    //             $query->with(['category', 'subcategory', 'sizes'])->get()
    //         );
    //     }
    public function filter(Request $request)
    {
        $sort = $request->query('sort');
        $categoryId = $request->query('category_id');
        $gender = $request->query('gender');
        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');
        $clothingSizes = $request->query('clothing_sizes'); // CSV: e.g. "XS,S,M"
        $shoeSizes = $request->query('shoe_sizes');         // CSV: e.g. "US M 7 / W 8.5,US M 9 / W 10.5"
        $color = $request->query('color');                  // e.g. "black"

        $query = Product::query();

        // Category filter
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Gender filter
        if ($gender) {
            $query->where('gender', $gender);
        }

        // Price range filter
        if ($minPrice && $maxPrice) {
            $query->whereBetween('price', [(float) $minPrice, (float) $maxPrice]);
        }

        // Color filter
        if ($color) {
            $query->where('color', $color);
        }

        // Clothing sizes filter
        if ($clothingSizes) {
            $sizes = explode(',', $clothingSizes);
            $query->whereHas('sizes', function ($q) use ($sizes) {
                $q->where('sizetype', 'clothing')->whereIn('sizevalue', $sizes);
            });
        }

        // Shoe sizes filter
        if ($shoeSizes) {
            $sizes = explode(',', $shoeSizes);
            $query->whereHas('sizes', function ($q) use ($sizes) {
                $q->where('sizetype', 'shoes')->whereIn('sizevalue', $sizes);
            });
        }

        // Sorting
        if ($sort === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('price', 'desc');
        } elseif ($sort === 'newest') {
            $query->orderBy('created_at', 'desc');
        }

        $products = $query->with(['category', 'subcategory', 'sizes'])->get();

        return response()->json($products);
    }
}
