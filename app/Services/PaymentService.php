<?php 
namespace App\Services;

use Exception;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function handleStripePayment($amount, $paymentMethodId)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $amountInCents = round($amount * 100);

        try {
            return $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $amountInCents,
                'currency' => 'usd',
                'payment_method' => $paymentMethodId,
                'confirm' => true,
                'automatic_payment_methods' => [
                    'enabled' => true,
                     'allow_redirects' => 'never',  // <-- This disables redirects
                ],
                // No 'confirmation_method' here!
            ]);

            // if ($paymentIntent->status === 'succeeded') {
            //     Log::info('PaymentIntent succeeded for amount ' . $amountInCents);
            //     return response()->json([
            //         'success' => true,
            //         'payment_intent_id' => $paymentIntent->id,
            //     ]);
            // } else if ($paymentIntent->status === 'requires_action') {
            //     // This means customer needs to do 3D Secure auth or similar
            //     return response()->json([
            //         'requires_action' => true,
            //         'payment_intent_client_secret' => $paymentIntent->client_secret,
            //     ]);
            // } else {
            //     // Other statuses, treat as failure or pending
            //     Log::warning('Unexpected PaymentIntent status: ' . $paymentIntent->status);
            //     return response()->json([
            //         'error' => 'Payment failed or requires additional actions.',
            //     ], 400);
            // }
        } catch (\Exception $e) {
            Log::error('PaymentIntent creation failed: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }

        // try {
        //     Stripe::setApiKey(env('STRIPE_SECRET'));

        //     $paymentIntent = PaymentIntent::create([
        //         'amount' => intval($amount * 100), // amount in cents
        //         'currency' => 'usd',
        //         'payment_method' => $paymentMethodId,
        //         'receipt_email' => $email,
        //         'confirmation_method' => 'manual',
        //         'confirm' => true,
        //     ]);

        //     return $paymentIntent;
        // } catch (ApiErrorException $e) {
        //     throw new Exception('Stripe payment failed: ' . $e->getMessage());
        // }
    }
}
