<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class PaypalController extends Controller
{
    public function getClientId()
    {
        return response()->json([
            'clientId' => env('PAYPAL_CLIENT_ID')
        ]);
    }

    public function captureOrder(Request $request)
    {
        $orderId = $request->orderID;

        // Get access token
        $auth = Http::asForm()->withBasicAuth(
            env('PAYPAL_CLIENT_ID'), env('PAYPAL_SECRET')
        )->post("https://api-m.sandbox.paypal.com/v1/oauth2/token", [
            'grant_type' => 'client_credentials'
        ]);

        $accessToken = $auth['access_token'];

        // Capture payment
        $response = Http::withToken($accessToken)
            ->post("https://api-m.sandbox.paypal.com/v2/checkout/orders/{$orderId}/capture");

        return response()->json($response->json());
    }
}
