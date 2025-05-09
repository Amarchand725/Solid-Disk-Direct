<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class FedExShippingService
{
    public function getRates($requestData)
    {
        $url = "https://apis.fedex.com/rate/v1/rates/quotes";

        $response = Http::withToken('l7339cad9a69b442ebb4e275e15eae7c1f')
            ->post($url, [
                "accountNumber" => [
                    "value" => "6e0e352b2ee5422f8d2e83b1696e60f4"
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

        return $response->json();
    }
}
