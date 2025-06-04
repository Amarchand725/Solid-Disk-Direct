<?php 
namespace App\Services\Payment\Gateways;

use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;

class PayarcGateway implements PaymentGatewayInterface
{
    protected string $securityKey;
    protected string $endpoint;

    public function __construct()
    {
        $this->securityKey = config('services.payarc.secret_key'); // must match your config
        $this->endpoint = config('services.payarc.endpoint', 'https://secure.networkmerchants.com/api/transact.php');
    }

    public function capture(array $data): array
    {
        $payload = [
            'security_key' => $this->securityKey,
            'amount' => $data['amount'],
            'ccnumber' => $data['card_number'],
            'ccexp' => $data['expiry'], // Format: MMYY
            'cvv' => $data['cvv'],
            'type' => 'sale',
            'firstname' => $data['firstname'] ?? '',
            'lastname' => $data['lastname'] ?? '',
            'email' => $data['email'] ?? '',
            'billing_address1' => $data['address'] ?? '',
            'billing_zip' => $data['zip'] ?? '',
        ];

        $response = Http::asForm()->post($this->endpoint, $payload);

        parse_str($response->body(), $result);

        return $result;
    }

    public function refund(string $transactionId, float $amount): array
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