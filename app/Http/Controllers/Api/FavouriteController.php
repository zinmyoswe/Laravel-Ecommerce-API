<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Favourite;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;

class FavouriteController extends Controller
{
    // Get all favourites for logged-in user
    public function index()
    {
        // $favourites = Favourite::with('product')
        //     ->where('user_id', Auth::id())
        //     ->get();

         $favourites = Favourite::with([
        'product.sizes', // 👈 include sizes on product
        ])->where('user_id', Auth::id())->get();

        return response()->json($favourites);
    }

    // Add to favourites
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,productid',
            'size' => 'nullable|string',
        ]);

        $favourite = Favourite::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
            ],
            [
                'size' => $request->size,
            ]
        );

        return response()->json($favourite, 201);
    }

    // Remove from favourites
    public function destroy($product_id)
    {
        $deleted = Favourite::where('user_id', Auth::id())
            ->where('product_id', $product_id)
            ->delete();

        return response()->json(['deleted' => $deleted > 0]);
    }
}
