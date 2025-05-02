<?php 
namespace App\Services;

use Exception;
use Stripe\Charge;
use Stripe\Stripe;

class PaymentService
{
    public function handleStripePayment($amount, $token, $email)
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $charge = Charge::create([
                'amount' => intval($amount * 100), // convert to cents
                'currency' => 'usd',
                'source' => $token,
                'description' => 'Order by ' . $email,
            ]);

            return $charge;
        } catch (Exception $e) {
            throw new Exception('Stripe payment failed: ' . $e->getMessage());
        }
    }
}
