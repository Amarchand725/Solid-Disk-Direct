<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class FedExShippingService
{
    protected $fedexClientId = "l7514709c23aaf49a9a5e3cd65c2b1da6c";
    protected $fedexSecretId = "0b649be5b6384d46a08ea15cca77e082";
    protected $fedexAccountNumber = "205370350";

    public function getRates(array $requestData)
    {
        $token = $this->getFedExAccessToken();

        $url = "https://apis.fedex.com/rate/v1/rates/quotes";

        $weightUnits = 'LB' ?? 'KG';
        $weightValue = $requestData['weight'];

        $payload = [
            "accountNumber" => [
                "value" => $this->fedexAccountNumber
            ],
            "requestedShipment" => [
                "shipper" => [
                    "address" => [
                        "postalCode"  => "60148", // your warehouse ZIP
                        "countryCode" => "US"
                    ]
                ],
                "recipient" => [
                    "address" => [
                        "postalCode"  => $requestData['postal_code'],
                        "countryCode" => $requestData['country_code']
                    ]
                ],
                "pickupType" => "DROPOFF_AT_FEDEX_LOCATION",
                "rateRequestType" => ["ACCOUNT"],
                "requestedPackageLineItems" => [
                    [
                        "weight" => [
                            "units" => $weightUnits,
                            "value" => $weightValue
                        ]
                    ]
                ]
            ]
        ];

        $response = Http::withToken($token)
            ->post($url, $payload);

        if ($response->failed()) {
            throw new \Exception('FedEx Rate Error: ' . $response->body());
        }

        return $response->json();
    }
    
    public function getFedExAccessToken()
    {
        $response = Http::asForm()->post('https://apis.fedex.com/oauth/token', [
            'grant_type'    => 'client_credentials',
            'client_id'     => $this->fedexClientId,
            'client_secret' => $this->fedexSecretId,
        ]);

        if ($response->failed()) {
            throw new \Exception('FedEx Auth Error: ' . $response->body());
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
