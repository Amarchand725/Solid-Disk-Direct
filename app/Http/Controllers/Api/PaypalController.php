<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class PaypalController extends Controller
{
    public function paypalSuccess(Request $request)
    {
        $token = $request->query('token'); 
        $order = Order::where('paypal_order_id', $token)->first();
        
        if ($order) {
            $order->update([
                'order_status' => 'pending',
                'payment_status' => 'paid',
            ]);

            if(sendOrderNotificationAndEmails($order)){
                Log::info('Paypal Order Emails Sent to Admin & Customer Successfully ! Order Number: '.$order->order_number);
            }else{
                Log::info('Paypal Order Emails not sent to Admin & Customer Failed ! Order Number: '.$order->order_number);
            }

            return redirect()->to(env('FRONTEND_BASE_URL') . "/order-success/{$order->order_number}");

        }

        return redirect()->route('checkout')->with('error', 'Payment was not successful.');
    }

    public function paypalCancel(Request $request)
    {
        return redirect()->route('checkout')->with('error', 'You cancelled the PayPal payment.');
    }
}
