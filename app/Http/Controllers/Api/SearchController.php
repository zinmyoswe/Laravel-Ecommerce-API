<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\ShopBySport;

class SearchController extends Controller
{
    // public function search(Request $request)
    // {
    //     $query = $request->input('q');

    //     if (!$query) {
    //         return response()->json([
    //             'message' => 'No search query provided.',
    //             'products' => [],
    //         ]);
    //     }

    //     $products = Product::where('productname', 'like', '%' . $query . '%')
    //         ->orWhere('description', 'like', '%' . $query . '%')
    //         ->orWhere('color', 'like', '%' . $query . '%')
    //         ->with(['sizes'])
    //         ->latest()
    //         ->limit(20)
    //         ->get();

    //     return response()->json([
    //         'products' => $products
    //     ]);
    // }
    public function search(Request $request)
{
    $query = $request->input('q');

    if (!$query) {
        return response()->json([
            'message' => 'No search query provided.',
            'products' => [],
        ]);
    }

     $products = Product::with(['sizes', 'category', 'subcategory', 'shopbysport'])
        ->where(function ($q) use ($query) {
            $q->where('productname', 'like', '%' . $query . '%')
              ->orWhere('color', 'like', '%' . $query . '%')
              ->orWhere('gender', 'like', '%' . $query . '%')
              ->orWhereHas('category', function ($catQuery) use ($query) {
                  $catQuery->where('categoryname', 'like', '%' . $query . '%');
              })
              ->orWhereHas('subcategory', function ($subQuery) use ($query) {
                  $subQuery->where('subcategoryname', 'like', '%' . $query . '%');
              })
              ->orWhereHas('shopbysport', function ($sportQuery) use ($query) {
                  $sportQuery->where('sportname', 'like', '%' . $query . '%');
              });
        })
        ->latest()
        ->limit(20)
        ->get();

    return response()->json([
        'products' => $products
    ]);
}
}
