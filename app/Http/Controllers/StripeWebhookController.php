<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $event = json_decode($payload);

        // You can log to storage/logs/laravel.log for debugging
        Log::info('Stripe Webhook:', ['event' => $event]);

        if ($event->type === 'charge.succeeded') {
            $charge = $event->data->object;

            // Example: 'ch_1P1rW2CxXzXXYYZZ'
            $chargeId = $charge->id;

            // Find order by stripe_charge_id
            $order = Order::where('stripe_charge_id', $chargeId)->first();

            if ($order && $order->status === 'pending') {
                $order->update(['status' => 'paid']);
                Log::info("Order #{$order->id} marked as paid.");
            }
        }

        return response()->json(['status' => 'success']);
    }
}
