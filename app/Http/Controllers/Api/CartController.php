<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;

class CartController extends Controller
{
    // Get cart items for user or session
    public function index(Request $request)
    {
        if (auth()->check()) {
            return response()->json(Cart::with('product')->where('user_id', auth()->id())->get());
        } elseif ($request->hasHeader('Session-Id')) {
            $sessionId = $request->header('Session-Id');
            return response()->json(Cart::with('product')->where('session_id', $sessionId)->get());
        }

        return response()->json(['error' => 'Unauthenticated or missing Session-Id'], 401);
    }

    // Add to cart
    

    public function store(Request $request)
        {
            $request->validate([
                'product_id' => 'required|exists:products,productid',
                'quantity' => 'required|integer|min:1',
                'size' => 'required|string',
                'session_id' => 'nullable|string',
            ]);

            $quantity = $request->quantity;
            $productId = $request->product_id;
            $size = $request->size;

            if (auth()->check()) {
                // Authenticated user → store in DB
                $existing = Cart::where('user_id', auth()->id())
                    ->where('product_id', $productId)
                    ->where('size', $size)
                    ->first();

                if ($existing) {
                    $existing->quantity += $quantity;
                    $existing->save();
                    $cart = $existing;
                } else {
                    $cart = Cart::create([
                        'user_id' => auth()->id(),
                        'product_id' => $productId,
                        'size' => $size,
                        'quantity' => $quantity,
                    ]);
                }

                return response()->json(['message' => 'Added to user cart', 'data' => $cart]);
            } else {
                // Guest user → store in DB using session_id
                $sessionId = $request->session_id;

                $existing = Cart::where('session_id', $sessionId)
                    ->where('product_id', $productId)
                    ->where('size', $size)
                    ->first();

                if ($existing) {
                    $existing->quantity += $quantity;
                    $existing->save();
                    $cart = $existing;
                } else {
                    $cart = Cart::create([
                        'session_id' => $sessionId,
                        'product_id' => $productId,
                        'size' => $size,
                        'quantity' => $quantity,
                    ]);
                }

                return response()->json(['message' => 'Added to guest cart (DB)', 'data' => $cart]);
            }
        }



    // Update quantity
    public function update(Request $request, $product_id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        if (auth()->check()) {
            $cart = Cart::where('user_id', auth()->id())->where('product_id', $product_id)->firstOrFail();
        } elseif ($request->hasHeader('Session-Id')) {
            $cart = Cart::where('session_id', $request->header('Session-Id'))->where('product_id', $product_id)->firstOrFail();
        } else {
            return response()->json(['error' => 'Missing Session-Id'], 400);
        }

        $cart->update(['quantity' => $request->quantity]);
        return response()->json($cart);
    }

    // Remove item
    public function destroy(Request $request, $product_id)
    {
        if (auth()->check()) {
            Cart::where('user_id', auth()->id())->where('product_id', $product_id)->delete();
        } elseif ($request->hasHeader('Session-Id')) {
            Cart::where('session_id', $request->header('Session-Id'))->where('product_id', $product_id)->delete();
        } else {
            return response()->json(['error' => 'Missing Session-Id'], 400);
        }

        return response()->json(['message' => 'Item removed']);
    }

    // Fetch by session explicitly
    public function showBySession($session_id)
    {
        $cartItems = Cart::where('session_id', $session_id)->with('product')->get();
        return response()->json($cartItems);
    }
}
