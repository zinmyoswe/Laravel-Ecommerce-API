<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;
use App\Models\Order;

class PaymentController extends Controller
{
    public function charge(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:50', // at least 50 cents
            'token' => 'required|string',
            'order_id' => 'required|integer', // 👈 required to update that order
        ]);

        Stripe::setApiKey(env('STRIPE_SECRET')); // set your secret key from .env

        try {
            $charge = Charge::create([
                'amount' => $request->amount * 100,
                'currency' => 'usd',
                'source' => $request->token,
                'description' => 'E-commerce payment',
            ]);

            // ✅ Update order status to 'paid' and save charge_id
            $order = Order::find($request->order_id);
            if ($order) {
                $order->status = 'paid';
                $order->stripe_charge_id = $charge->id;
                $order->save();
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
