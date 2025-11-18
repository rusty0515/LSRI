<?php

namespace App\Http\Controllers; // Or add to your Livewire component

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Luigel\Paymongo\Facades\Paymongo;

class PaymentController extends Controller
{
    public function paymentCallback(Request $request)
    {
        $paymentIntentId = $request->query('payment_intent_id'); //query payment_intent_id from paymongo
        // dd($paymentIntentId);
        if ($paymentIntentId) {
            try {
                $paymentIntent = Paymongo::paymentIntent()->find($paymentIntentId)->getAttributes();

                $order = Order::where('payment_intent_id', $paymentIntentId)->first();
                if ($order) {
                   $status = $paymentIntent['status'];
                    $newStatus = 'failed'; // default

                    if ($status === 'succeeded') {
                        $newStatus = 'paid';
                      
                    } elseif (in_array($status, ['awaiting_payment', 'processing'])) {
                        $newStatus = 'pending';
                    } elseif ($status === 'expired') {
                        $newStatus = 'failed';
                    }

                    $order->update(['payment_status' => $newStatus]);

                    session()->forget([
                        'cart',
                        'sub_total',
                    ]);

                    return redirect()->route('page.customer-dashboard')->with('message', 'Product ordered successfully');
                }
            } catch (\Exception $e) {
                // Log error
                Log::error("Payment callback error: " . $e->getMessage());
            }
        }

        return redirect()->route('page.customer-dashboard');
    }
}
