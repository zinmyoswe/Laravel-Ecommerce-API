<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;

class PaymentController extends Controller
{
    public function charge(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:50', // at least 50 cents
            'token' => 'required|string',
        ]);

        Stripe::setApiKey(env('STRIPE_SECRET')); // set your secret key from .env

        try {
            $charge = Charge::create([
                'amount' => $request->amount,
                'currency' => 'usd',
                'source' => $request->token,
                'description' => 'E-commerce payment',
            ]);

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
