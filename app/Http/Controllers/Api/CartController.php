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
            return response()->json(
                Cart::with('product')->where('user_id', auth()->id())->get()
            );
        } elseif ($request->hasHeader('Session-Id')) {
            $sessionId = $request->header('Session-Id');
            return response()->json(
                Cart::with('product')->where('session_id', $sessionId)->get()
            );
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
            // Guest user → store using Session-Id header
            $sessionId = $request->header('Session-Id');
            if (!$sessionId) {
                return response()->json(['error' => 'Missing Session-Id'], 400);
            }

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

            return response()->json(['message' => 'Added to guest cart', 'data' => $cart]);
        }
    }


public function update(Request $request, $product_id)
{
    $request->validate([
        'quantity' => 'required|integer|min:1',
        'size' => 'required|string',
    ]);

    // ✅ Try to get user ID
    $userId = auth('sanctum')->id(); // ✔ Use 'sanctum'
    $sessionId = $request->header('Session-Id');

    // ❗ fallback check
    if (!$userId && !$sessionId) {
        return response()->json(['message' => 'Session ID or Auth token required'], 400);
    }

    // 🔍 Start query
    $query = Cart::where('product_id', $product_id)
                 ->where('size', $request->size);

    if ($userId) {
        $query->where('user_id', $userId);
    } else {
        $query->where('session_id', $sessionId);
    }

    $cartItem = $query->first();

    if (!$cartItem) {
        return response()->json(['message' => 'Cart item not found'], 404);
    }

    $cartItem->quantity = $request->quantity;
    $cartItem->save();

    return response()->json(['message' => 'Cart updated']);
}


public function updateForGuest(Request $request, $product_id)
{
    $sessionId = $request->header('Session-Id');
    if (!$sessionId) {
        return response()->json(['message' => 'Session ID required'], 400);
    }

    $request->validate([
        'quantity' => 'required|integer|min:1',
        'size' => 'required|string',
    ]);

    $cartItem = Cart::where('product_id', $product_id)
                    ->where('size', $request->size)
                    ->where('session_id', $sessionId)
                    ->first();

    if (!$cartItem) {
        return response()->json(['message' => 'Cart item not found'], 404);
    }

    $cartItem->quantity = $request->quantity;
    $cartItem->save();

    return response()->json(['message' => 'Cart updated']);
}




  
 // 🔸 Remove Item (Both Guest and Logged-in)
    public function destroy(Request $request, $product_id)
    {
        $user = $request->user();

        if ($user) {
            $deleted = Cart::where('user_id', $user->id)
                        ->where('product_id', $product_id)
                        ->delete();
        } elseif ($request->hasHeader('Session-Id')) {
            $deleted = Cart::where('session_id', $request->header('Session-Id'))
                        ->where('product_id', $product_id)
                        ->delete();
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if ($deleted) {
            return response()->json(['message' => 'Cart item removed']);
        }

        return response()->json(['error' => 'Cart item not found'], 404);
    }

    // Fetch by session explicitly
    public function showBySession($session_id)
    {
            $cartItems = Cart::with('product')
                ->where('session_id', $session_id)
                ->get();

            return response()->json($cartItems);
    }

        public function storeForGuest(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
            'product_id' => 'required|exists:products,productid',
            'size' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = Cart::where('session_id', $request->session_id)
            ->where('product_id', $request->product_id)
            ->where('size', $request->size)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            $cartItem = Cart::create([
                'session_id' => $request->session_id,
                'product_id' => $request->product_id,
                'size' => $request->size,
                'quantity' => $request->quantity,
            ]);
        }

        return response()->json($cartItem);
    }

    public function destroyForGuest(Request $request, $product_id)
{
    $sessionId = $request->header('Session-Id');
    $size = $request->input('size');

    if (!$sessionId || !$size) {
        return response()->json(['message' => 'Session-Id header and size are required'], 400);
    }

    $cartItem = Cart::where('session_id', $sessionId)
        ->where('product_id', $product_id)
        ->where('size', $size)
        ->first();

    if (!$cartItem) {
        return response()->json(['message' => 'Cart item not found'], 404);
    }

    $cartItem->delete();

    return response()->json(['message' => 'Item removed from guest cart']);
}


}
