<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class PayarcService
{
    protected $baseUrl = 'https://testapi.payarc.net/v1'; // Confirm this base URL from docs

    public function createPaymentIntent(array $data)
    {
        // 1. Tokenize the card
        $tokenResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('payarc.token'),
            'Accept' => 'application/json',
        ])->post("{$this->baseUrl}/tokens", [
            'card_source' => 'INTERNET',
            'card_number' => $data['card_number'],
            'exp_month'   => $data['exp_month'],
            'exp_year'    => $data['exp_year'],
            'cvv'         => $data['cvv'],
            'amount'      => $data['amount'],
            'currency'    => $data['currency'],
            'card_holder_name' => $data['card_holder'],
        ]);

        if ($tokenResponse->failed()) {
            Log::error('Token creation failed: ' . $tokenResponse->body());
            return ['error' => 'Tokenization failed', 'details' => $tokenResponse->json()];
        }

        $tokenId = $tokenResponse['data']['id'];
        Log::info('TokenId: ' . $tokenId);

        // 2. Make the payment
        $paymentResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('payarc.token'),
            'Accept' => 'application/json',
        ])->post("{$this->baseUrl}/charges", [
            'token_id' => $tokenId,
            'amount'   => $data['amount'],
            'currency' => $data['currency'],
            'description' => 'Test payment',
            'capture'     => true,
        ]);

        Log::info('Payment response status: ' . $paymentResponse->status());
        // Log::info('Payment response body: ' . $paymentResponse->body());
        // Log::info('Payment response JSON: ', $paymentResponse->json());

        return $paymentResponse->json();
    }
    // public function createPaymentIntent(array $data)
    // {
    //     $response = Http::withHeaders([
    //         'Authorization' => 'Bearer '. config('payarc.token'),
    //         'Accept' => 'application/json',
    //     ])->post('https://testapi.payarc.net/v1/tokens', [
    //         'card_source' => 'INTERNET',
    //         'card_number' => '4012001038443335',   // Test Visa
    //         'exp_month'   => '12',
    //         'exp_year'    => '2025',
    //         'cvv'         => '999',
    //         'amount'      => 1000, // in cents
    //         'currency'    => 'USD',
    //         'card_holder' => 'John Doe',
    //     ]);

    //     $tokenId = $response['data']['id'];
    //     Log::info('TokenId: '. $tokenId);
    //     $paymentResponse = Http::withHeaders([
    //         'Authorization' => 'Bearer ' . config('payarc.token'),
    //         'Accept' => 'application/json',
    //     ])->post('https://testapi.payarc.net/v1/charges', [
    //         'token_id'    => $tokenId,
    //         'amount'      => 1000,
    //         'currency' => 'usd',
    //         'description' => 'Test payment',
    //         'capture'     => true,
    //     ]);

    //     Log::info('Payment response status: ' . $paymentResponse->status());
    //     Log::info('Payment response body: ' . $paymentResponse->body());
    //     Log::info('Payment response JSON: ', $paymentResponse->json());

    //     return $paymentResponse;
    // }

    public function getPaymentMethods()
    {
        return Http::withToken(config('payarc.token'))
            ->get($this->baseUrl . '/v1/payment-methods')
            ->json();
    }
}
