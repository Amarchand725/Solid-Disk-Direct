<?php 
namespace App\Services\Payment;

use Exception;
use Illuminate\Support\Facades\Http;
use App\Services\Payment\Gateways\PayPalGateway;
use App\Services\Payment\Contracts\PaymentGatewayInterface;

class PaymentService
{
    protected $clientId;
    protected $secret;
    protected $baseUrl;
    protected PaymentGatewayInterface $gateway;

    public function __construct(string $method)
    {
        $this->gateway = match ($method) {
            'paypal' => new PayPalGateway(),
            default => throw new Exception("Unsupported payment gateway: {$method}")
        };

        if ($method === 'paypal') {
            $this->clientId = config('services.paypal.client_id');
            $this->secret = config('services.paypal.secret');
            $this->baseUrl = 'https://api-m.sandbox.paypal.com'; // or use live for production
        }
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

    public function capture($data)
    {
        return $this->gateway->capture($data);
    }

    // public function refund(string $transactionId, float $amount)
    // {
    //     return $this->gateway->refund($transactionId, $amount);
    // }

    public function createPaypalOrder($order)
    {
        $accessToken = $this->getAccessToken()['access_token'];

        $payload = [
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => number_format($order->total, 2, '.', ''),
                    ]
                ]
            ],
            "application_context" => [
                "return_url" => route('paypal.success'),  // your success callback URL
                "cancel_url" => route('paypal.cancel'),  // your cancel callback URL
            ]
        ];

        $response = Http::withToken($accessToken)
            ->post("{$this->baseUrl}/v2/checkout/orders", $payload);

        if (!$response->successful()) {
            throw new Exception('Failed to create PayPal order: ' . $response->body());
        }

        return $response->json();  // contains "id" and "links"
    }
}