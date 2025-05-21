<?php 
namespace App\Services\Payment\Gateways;

use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;

class PayarcGateway implements PaymentGatewayInterface
{
    protected $securityKey;
    protected $endpoint;

    public function __construct()
    {
        $this->securityKey = config('services.payarc.security_key');
        $this->endpoint = config('services.payarc.endpoint', 'https://secure.networkmerchants.com/api/transact.php');
    }

    public function capture(array $data)
    {
        $payload = [
            'security_key' => $this->securityKey,
            'amount' => $data['amount'],
            'ccnumber' => $data['card_number'],
            'ccexp' => $data['expiry'],     // Format: MMYY
            'cvv' => $data['cvv'],
            'type' => 'sale',
            'firstname' => $data['firstname'] ?? '',
            'lastname' => $data['lastname'] ?? '',
            'email' => $data['email'] ?? '',
            'billing_address1' => $data['address'] ?? '',
            'billing_zip' => $data['zip'] ?? '',
            // Add more fields if needed
        ];

        $response = Http::asForm()->post($this->endpoint, $payload);

        parse_str($response->body(), $result);

        return $result;
    }

    public function refund(string $transactionId, float $amount)
    {
        $payload = [
            'security_key' => $this->securityKey,
            'transactionid' => $transactionId,
            'amount' => $amount,
            'type' => 'refund',
        ];

        $response = Http::asForm()->post($this->endpoint, $payload);

        parse_str($response->body(), $result);

        return $result;
    }
}