<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;  // Import Log facade


class ProductController extends Controller
{
    // List all products
    


public function index(Request $request)
{
    $query = Product::with(['category', 'subcategory', 'shopBySport', 'similarProducts', 'sizes']);

    // Search filter

    if ($request->filled('search')) {
    $keyword = $request->search;
    $query->where(function ($q) use ($keyword) {
        $q->where('productname', 'like', "%{$keyword}%")
          ->orWhereHas('subcategory', function ($q) use ($keyword) {
              $q->where('subcategoryname', 'like', "%{$keyword}%");
          });
    });
}


    // Subcategory filter: support both single and multiple IDs
    if ($request->filled('subcategory_id')) {
        $subcategoryIds = $request->input('subcategory_id');
        if (is_array($subcategoryIds)) {
            $query->whereIn('subcategory_id', $subcategoryIds);
        } else {
            $query->where('subcategory_id', $subcategoryIds);
        }
    }

    // Gender filter (single value)
    // if ($request->filled('gender')) {
    //     $query->where('gender', $request->input('gender'));
    // }

    if ($request->filled('gender')) {
    $gender = $request->input('gender');
    if (is_array($gender)) {
        $query->whereIn('gender', $gender);
    } else {
        $query->where('gender', $gender);
    }
}

    // Color filter (single value)
    // if ($request->filled('color')) {
    //     $query->where('color', $request->input('color'));
    // }

if ($request->filled('color')) {
    $color = $request->input('color');
    if (is_array($color)) {
        $query->whereIn('color', $color);
    } else {
        $query->where('color', $color);
    }
}

    // Size filter (single value)
    // if ($request->filled('sizevalue')) {
    //     $query->whereHas('sizes', function ($q) use ($request) {
    //         $q->where('sizevalue', $request->input('sizevalue'));
    //     });
    // }

if ($request->filled('sizevalue')) {
    $sizes = $request->input('sizevalue');
    $query->whereHas('sizes', function ($q) use ($sizes) {
        if (is_array($sizes)) {
            $q->whereIn('sizevalue', $sizes);
        } else {
            $q->where('sizevalue', $sizes);
        }
    });
}

//    if ($request->has('shopbysport_id')) {
//     $query->where('shopbysport_id', $request->shopbysport_id);
// }

    if ($request->has('shopbysportId')) {
    $shopbysportId = $request->input('shopbysportId');
    $query->where('shopbysport_id', $shopbysportId);
}

    // Price filter (single value)
    // if ($request->filled('price')) {
    //     $price = $request->input('price');
    //     $query->where(function ($q) use ($price) {
    //         switch ($price) {
    //             case 'under_50':
    //                 $q->where('price', '<', 50);
    //                 break;
    //             case '50_100':
    //                 $q->whereBetween('price', [50, 100]);
    //                 break;
    //             case '101_199':
    //                 $q->whereBetween('price', [101, 199]);
    //                 break;
    //             case 'over_200':
    //                 $q->where('price', '>', 200);
    //                 break;
    //         }
    //     });
    // }

if ($request->filled('price')) {
    $prices = $request->input('price');

    if (!is_array($prices)) {
        $prices = [$prices];
    }

    $query->where(function ($q) use ($prices) {
        foreach ($prices as $price) {
            $q->orWhere(function ($subQ) use ($price) {
                switch ($price) {
                    case 'under_50':
                        $subQ->where('price', '<', 50);
                        break;
                    case '50_100':
                        $subQ->whereBetween('price', [50, 100]);
                        break;
                    case '101_199':
                        $subQ->whereBetween('price', [101, 199]);
                        break;
                    case 'over_200':
                        $subQ->where('price', '>', 200);
                        break;
                }
            });
        }
    });
}


    // Sorting
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
    } else {
        $query->orderBy('productid', 'desc');
    }

    //     // ✅ Fallback to latest by productid if no sort specified
    if (!$request->filled('sort')) {
        $query->orderBy('productid', 'desc');
    }

    if ($request->filled('limit')) {
        $query->limit((int) $request->input('limit'));
    }

    // return response()->json($query->get());

    // Ensure similarProducts eager loaded and trimmed
    $query->with(['similarProducts' => function ($q) {
        $q->select('productid', 'productimage');
    }]);

    // Get products and map to add similar_image
    $products = $query->get()->map(function ($product) {
        $product->similar_image = optional($product->similarProducts->first())->productimage;
        return $product;
    });

    // ✅ Return mapped products (with similar_image)
    return response()->json($products);
}

    

    public function create(Request $request)
{
    $validated = $request->validate([
        'productname' => 'required|string',
        'productimage' => 'required|url',
        'productimages' => 'nullable|array',
        'productimages.*' => 'url',
        'productvideo' => 'nullable|url',
        'category_id' => 'required|exists:categories,categoryid',
        'subcategory_id' => 'required|exists:subcategories,subcategoryid',
        'shopbysport_id' => 'nullable|exists:shopbysports,id', // ✅ Add this line
        'color' => 'required|string',
        'price' => 'required|numeric',
        'discount' => 'nullable|numeric',
        'stock' => 'required|integer',
        'description' => 'nullable|string',
        'gender' => 'required|string|in:Men,Women,Kids,Unisex',
        'adminid' => 'nullable|integer',
        'sameproductid' => 'nullable|exists:products,productid',
        'size_ids' => 'nullable|array',
        'size_ids.*' => 'exists:sizes,id',
    ]);

    $product = Product::create($validated);

    // Attach sizes if provided
    if ($request->has('size_ids')) {
        $product->sizes()->attach($validated['size_ids']);
    }

    return response()->json([
        'message' => 'Product created successfully',
        'product' => $product->load(['sizes', 'category', 'subcategory', 'shopBySport'])
    ], 201);
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
