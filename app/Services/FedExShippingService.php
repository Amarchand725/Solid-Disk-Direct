<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class FedExShippingService
{
    public function getRates($requestData)
    {
        $token = $this->getFedExAccessToken(); // get the valid token

        $url = "https://apis-sandbox.fedex.com/rate/v1/rates/quotes";

        $response = Http::withToken($token)
            ->post($url, [
                "accountNumber" => [
                    "value" => env('FEDEX_ACCOUNT_NUMBER')
                ],
                "requestedShipment" => [
                    "shipper" => [
                        "address" => [
                            "postalCode" => "12345",
                            "countryCode" => "US"
                        ]
                    ],
                    "recipient" => [
                        "address" => [
                            "postalCode" => $requestData['postal_code'],
                            "countryCode" => $requestData['country_code']
                        ]
                    ],
                    "pickupType" => "DROPOFF_AT_FEDEX_LOCATION",
                    "rateRequestType" => ["ACCOUNT"],
                    "requestedPackageLineItems" => [
                        [
                            "weight" => [
                                "units" => "KG",
                                "value" => $requestData['weight']
                            ]
                        ]
                    ]
                ]
            ]);

            if ($response->failed()) {
                throw new \Exception('FedEx Rate Error: ' . $response->body());
            }

            return $response->json();
    }

    public function getFedExAccessToken()
    {
        $response = Http::asForm()->post('https://apis-sandbox.fedex.com/oauth/token', [
            'grant_type' => 'client_credentials',
            'client_id' => env('FEDEX_CLIENT_ID'),
            'client_secret' => env('FEDEX_CLIENT_SECRET'),
        ]);

        if ($response->failed()) {
            throw new \Exception('FedEx Auth Failed: ' . $response->body());
        }

        return $response->json()['access_token'];
    }

   public function createShipment($shipmentData)
    {
        $token = $this->getFedExAccessToken();

        $response = Http::withToken($token)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post('https://apis-sandbox.fedex.com/ship/v1/shipments', $shipmentData);

        if ($response->failed()) {
            throw new \Exception('FedEx Shipment Error: ' . $response->body());
        }

        return $response->json();
    }
}
