<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Services\Payment\PaymentService;

class PaypalController extends Controller
{
    public function paypalSuccess(Request $request)
    {
        $token = $request->query('token');  // PayPal order ID from query param

        // $paymentService = new PaymentService('paypal');
        // $captureResponse = $paymentService->capture($token);

        // if (!empty($captureResponse['status']) && $captureResponse['status'] === 'COMPLETED') {
        $order = Order::where('paypal_order_id', $token)->first();

        if ($order) {
            $order->update([
                'order_status' => 'paid',
                'payment_status' => 'completed',
            ]);

            return redirect()->to("http://localhost:5173/order-success/{$order->order_number}");

        }
        // }

        return redirect()->route('checkout')->with('error', 'Payment was not successful.');
    }

    public function paypalCancel(Request $request)
    {
        return redirect()->route('checkout')->with('error', 'You cancelled the PayPal payment.');
    }
}
