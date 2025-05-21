<?php 
namespace App\Services\Payment;

use Exception;
use App\Services\Payment\Gateways\PayarcGateway;
use App\Services\Payment\Gateways\PayPalGateway;
use App\Services\Payment\Contracts\PaymentGatewayInterface;

class PaymentService
{
    protected PaymentGatewayInterface $gateway;

    public function __construct(string $method)
    {
        $this->gateway = match ($method) {
            // 'paypal' => new PayPalGateway(),
            'payarc' => new PayarcGateway(),
            default => throw new Exception("Unsupported payment gateway: {$method}")
        };
    }

    public function capture(array $data)
    {
        return $this->gateway->capture($data);
    }

    public function refund(string $transactionId, float $amount)
    {
        return $this->gateway->refund($transactionId, $amount);
    }
}