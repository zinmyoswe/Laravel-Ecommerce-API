<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Customer;
use Stripe\Stripe;
use Stripe\Charge;
use App\Models\Order;

class PaymentController extends Controller
{
    public function charge(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:1',
            'token' => 'required|string',
            'order_id' => 'required|integer',
            'name' => 'nullable|string|max:255',
        ]);

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // ✅ Step 1: Create a Stripe customer with name and token
            $customer = Customer::create([
                'name' => $request->name ?? 'Guest',
                'source' => $request->token,
            ]);

            // ✅ Step 2: Charge the created customer
            $charge = Charge::create([
                'amount' => $request->amount * 100, // Stripe expects cents
                'currency' => 'usd',
                'customer' => $customer->id,
                'description' => 'E-commerce payment for order ID ' . $request->order_id,
            ]);

            // ✅ Step 3: Update order status

             $order = Order::with('items.product')->find($request->order_id);

            if ($order) {
                $order->update([
                    'status' => 'paid',
                    'stripe_charge_id' => $charge->id,
                ]);

                foreach ($order->items as $item) {
                    $item->product->decrement('stock', $item->quantity);
                }
            }

            return response()->json([
                'message' => 'Payment successful',
                'charge_id' => $charge->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Payment failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
