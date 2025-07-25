<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Shipping;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'session_id' => 'nullable|string',
            'payment_method' => 'required|string',

            // Shipping validation
            'shipping.name' => 'required|string',
            'shipping.email' => 'required|email',
            'shipping.phone' => 'required|string',
            'shipping.address' => 'required|string',
            'shipping.city' => 'required|string',
            'shipping.postal_code' => 'required|string',
            'shipping.country' => 'required|string',
            'stripe_charge_id' => 'nullable|string',
            'shipping.shipping_fee' => 'required|numeric|min:0',
            'shipping.delivery_option' => 'required|string',
        ]);

        $userId = auth()->id();
        $sessionId = $request->session_id;

        // Get cart items
        $cartItems = Cart::where(function ($query) use ($userId, $sessionId) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_id', $sessionId);
            }
        })->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        // Calculate total
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item->product->price * $item->quantity;
        }

        // ✅ Correct way to extract shipping data
        $shippingData = $request->input('shipping');
        $shippingFee = $shippingData['shipping_fee'];
        $deliveryOption = $shippingData['delivery_option'];
        $grandTotal = $total + $shippingFee;



        // Create order
        $order = Order::create([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'total' => $total,
            'shipping_fee' => $shippingFee,
            'delivery_option' => $deliveryOption,
            'payment_method' => $request->payment_method,
            'status' => 'pending',
            'stripe_charge_id' => $request->stripe_charge_id ?? null, // from payment response
        ]);

        // Create order items
        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'size' => $item->size,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
            ]);

            // Decrease stock
            // $item->product->decrement('stock', $item->quantity);
        }

        // Create shipping info
        // Shipping::create([
        //     'order_id' => $order->id,
        //     'name' => $request->shipping['name'],
        //     'email' => $request->shipping['email'],
        //     'phone' => $request->shipping['phone'],
        //     'address' => $request->shipping['address'],
        //     'city' => $request->shipping['city'],
        //     'postal_code' => $request->shipping['postal_code'],
        //     'country' => $request->shipping['country'],
        // ]);
        Shipping::create([
            'order_id' => $order->id,
            'name' => $shippingData['name'],
            'email' => $shippingData['email'],
            'phone' => $shippingData['phone'],
            'address' => $shippingData['address'],
            'city' => $shippingData['city'],
            'postal_code' => $shippingData['postal_code'],
            'country' => $shippingData['country'],
        ]);

        // Clear cart
        Cart::where(function ($query) use ($userId, $sessionId) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_id', $sessionId);
            }
        })->delete();

        return response()->json([
            'message' => 'Order created successfully',
            'order_id' => $order->id,
            'shipping_fee' => $order->shipping_fee,
            'total' => $order->total,
            'grand_total' => $grandTotal
        ], 201);
    }


    // Get order history for authenticated user
    public function index()
    {
        $userId = auth()->id();

        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $orders = \App\Models\Order::with(['items.product', 'shipping'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }

    // Get order history for guest user by session_id
    public function guestOrders($sessionId)
    {
        $orders = \App\Models\Order::with(['items.product', 'shipping'])
            ->where('session_id', $sessionId)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($orders->isEmpty()) {
            return response()->json(['message' => 'No orders found for this session'], 404);
        }

        return response()->json($orders);
    }


    public function show($id)
    {
        $order = Order::with(['items.product', 'shipping', 'user'])->find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        return response()->json($order);
    }

    public function getUserOrders($user_id)
    {
        if (auth()->id() != $user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $orders = Order::with(['items.product', 'shipping'])
                    ->where('user_id', $user_id)
                    ->orderBy('created_at', 'desc')
                    ->get();

        return response()->json($orders);
    }

     // ✅ 2. MEMBER CHECKOUT (Authenticated via Sanctum)


public function storeForMember(Request $request)
{
    $request->validate([
        'payment_method' => 'required|string',
        'shipping.name' => 'required|string',
        'shipping.email' => 'required|email',
        'shipping.phone' => 'required|string',
        'shipping.address' => 'required|string',
        'shipping.city' => 'required|string',
        'shipping.postal_code' => 'required|string',
        'shipping.country' => 'required|string',
        'shipping.shipping_fee' => 'required|numeric|min:0',
        'shipping.delivery_option' => 'required|string',
    ]);

    $userId = auth()->id();

    if (!$userId) {
        return response()->json(['message' => 'Unauthorized. No user found.'], 401);
    }

    $cartItems = Cart::where('user_id', $userId)->with('product')->get();

    if ($cartItems->isEmpty()) {
        return response()->json(['message' => 'Cart is empty'], 400);
    }

    // Calculate subtotal
    $total = 0;
    foreach ($cartItems as $item) {
        $total += $item->product->price * $item->quantity;
    }

    // ✅ Correct way to extract shipping data
    $shippingData = $request->input('shipping');
    $shippingFee = $shippingData['shipping_fee'];
    $deliveryOption = $shippingData['delivery_option'];
    $grandTotal = $total + $shippingFee;

//     \Log::info('ShippingData:', $shippingData);
//     \Log::info('Saving order with:', [
//     'total' => $total,
//     'shipping_fee' => $shippingFee,
//     'delivery_option' => $deliveryOption,
// ]);

//     dd($shippingFee, $deliveryOption);
    // Create order
    $order = Order::create([
        'user_id' => $userId,
        'total' => $total,
        'shipping_fee' => $shippingFee,
        'delivery_option' => $deliveryOption,
        'payment_method' => $request->payment_method,
        'status' => 'pending',
    ]);

    // Create order items
    foreach ($cartItems as $item) {
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $item->product_id,
            'size' => $item->size,
            'quantity' => $item->quantity,
            'price' => $item->product->price,
        ]);
    }

    // Create shipping
    Shipping::create([
        'order_id' => $order->id,
        'name' => $shippingData['name'],
        'email' => $shippingData['email'],
        'phone' => $shippingData['phone'],
        'address' => $shippingData['address'],
        'city' => $shippingData['city'],
        'postal_code' => $shippingData['postal_code'],
        'country' => $shippingData['country'],
    ]);

    // Clear user cart
    Cart::where('user_id', $userId)->delete();

    return response()->json([
        'message' => 'Order created successfully',
        'order_id' => $order->id,
        'shipping_fee' => $order->shipping_fee,
        'total' => $order->total,
        'grand_total' => $grandTotal
    ], 201);
}

}
