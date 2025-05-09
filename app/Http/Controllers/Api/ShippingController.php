<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\FedExShippingService;

class ShippingController extends Controller
{
    public function getFedExRates(Request $request)
    {
        $fedex = new FedExShippingService();
        $rates = $fedex->getRates([
            'postal_code' => $request->zip_code,
            'country_code' => $request->country,
            'weight' => $request->weight ?? 1.0 // default 1kg
        ]);

        return response()->json($rates);
    }
}
