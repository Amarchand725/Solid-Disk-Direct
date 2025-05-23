<?php 
namespace App\Services\Payment\Gateways;

use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;

class PayPalGateway implements PaymentGatewayInterface
{
    protected $clientId;
    protected $secret;
    protected $baseUrl;

    public function __construct()
    {
        $this->clientId = config('services.paypal.client_id');
        $this->secret = config('services.paypal.secret');
        $this->baseUrl = config('services.paypal.mode') === 'sandbox'
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';
    }

    protected function getAccessToken()
    {
        $response = Http::asForm()->withBasicAuth($this->clientId, $this->secret)
            ->post("{$this->baseUrl}/v1/oauth2/token", [
                'grant_type' => 'client_credentials'
            ]);

        return $response['access_token'];
    }

    public function capture(array $data)
    {
        $accessToken = $this->getAccessToken();

        $response = Http::withToken($accessToken)
            ->post("{$this->baseUrl}/v2/checkout/orders/{$data['orderID']}/capture");

        return $response->json();
    }

    public function refund(string $transactionId, float $amount)
    {
        // Optional: Add refund logic here
    }
}
