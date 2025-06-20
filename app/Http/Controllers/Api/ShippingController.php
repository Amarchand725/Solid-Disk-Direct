<?php

namespace App\Http\Controllers\Api;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\FedExShippingService;

class ShippingController extends Controller
{
    public function getFedExRates(Request $request)
    {
        $country = Country::where('id', $request->country)->first();
        if($country){
            $fedex = new FedExShippingService();
            $rates = $fedex->getRates([
                'postal_code' => $request->zip_code,
                'country_code' => $country->code,
                'weight' => $request->weight ?? 1.0 // default 1kg
            ]);

            return response()->json([
                'status' => true,
                'message' => 'FedEx Rates Found.',
                'data' => $rates
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Country not matched.',
                'data' => null
            ]);
        }
    }
}
