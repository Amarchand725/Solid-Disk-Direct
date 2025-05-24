<?php 
namespace App\Services\Payment\Gateways;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Services\Payment\Contracts\PaymentGatewayInterface;

class PayPalGateway implements PaymentGatewayInterface
{
    protected $clientId;
    protected $secret;
    protected $baseUrl;

    public function __construct()
    {
        $this->clientId = config('services.paypal.client_id');
        $this->secret = config('services.paypal.secret');
        $this->baseUrl = 'https://api-m.sandbox.paypal.com';
    }

    protected function getAccessToken()
    {
        $response = Http::asForm()->withBasicAuth($this->clientId, $this->secret)
            ->post("{$this->baseUrl}/v1/oauth2/token", [
                'grant_type' => 'client_credentials'
            ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to get PayPal access token: ' . $response->body());
        }

        return $response->json();
    }

    public function create($order)
    {
        $accessToken = $this->getAccessToken()['access_token'];

        $response = Http::withToken($accessToken)->post("{$this->baseUrl}/v2/checkout/orders", [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'amount' => [
                    'currency_code' => 'USD',
                    'value' => number_format($order->total, 2, '.', '')
                ]
            ]],
            'application_context' => [
                'return_url' => route('paypal.success'),
                'cancel_url' => route('paypal.cancel')
            ]
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to create PayPal order: ' . $response->body());
        }

        return $response->json();
    }

    public function capture($order)
    {
        $paypalOrderId = $order->id;

        if (!$paypalOrderId) {
            throw new \Exception('PayPal order ID is missing');
        }

        $tokenData = $this->getAccessToken();
        if (!isset($tokenData['access_token'])) {
            throw new \Exception('Access token missing in PayPal response: ' . json_encode($tokenData));
        }
        $accessToken = $tokenData['access_token'];

        $response = Http::withToken($accessToken)       
            ->post("{$this->baseUrl}/v2/checkout/orders/{$paypalOrderId}/capture", []);

        // Log::info('PayPal Capture API Status: ' . $response->status());
        // Log::info('PayPal Capture API Response: ' . $response->body());

        if (!$response->successful()) {
            throw new \Exception('Failed to capture PayPal order: ' . $response->body());
        }

        return $response->json();
    }


    public function refund(string $transactionId, float $amount)
    {
        // Optional: Add refund logic here
    }
}
