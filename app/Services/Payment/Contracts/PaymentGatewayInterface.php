<?php 
namespace App\Services\Payment\Contracts;

interface PaymentGatewayInterface
{
    public function capture(array $data);
    public function refund(string $transactionId, float $amount);
    // Add more methods if needed: createIntent, cancel, etc.
}
