<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BuyNowController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_slug' => 'required|exists:products,slug',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::where('slug', $request->product_slug)->first();
        if(isset($product) && !empty($product)){
            session([
                'buy_now' => [
                    'product_slug' => $product->slug,
                    'quantity' => $request->input('quantity', 1),
                    'price' => $product->unit_price, // Snapshot of price
                ],
                'buy_now_totals' => null // Clear any previous calculation
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Failed to purchase product'
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function updateBuyNowShippingTax(Request $request)
    {
        $request->validate([
            'country' => 'required|string',
            'state' => 'nullable|string',
            'rate' => 'required|array' // Shipping rate object from frontend
        ]);

        $buyNow = session('buy_now');

        if (!$buyNow) {
            return response()->json([
                'success' => false,
                'message' => 'Buy Now session expired.'
            ], 422);
        }

        $product = Product::where('slug', $buyNow['product_slug'])->first();
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ], 404);
        }

        try {
            $quantity = $buyNow['quantity'] ?? 1;
            $unitPrice = $buyNow['price'] ?? $product->unit_price;
            $subtotal = $unitPrice * $quantity;

            // Shipping
            $rateObj = $request->rate;
            $shippingCost = $rateObj['totalCharge'] ?? 0;

            // Tax
            $taxInfo = $this->getTaxInfo($request->country, $request->state);
            $taxRate = isset($taxInfo['rate']) ? (float) $taxInfo['rate'] : 0;
            $taxAmount = round(($taxRate / 100) * $subtotal, 2);

            $total = round($subtotal + $shippingCost + $taxAmount, 2);

            // Store in session
            session([
                'buy_now_totals' => [
                    'subtotal' => $subtotal,
                    'tax_rate' => $taxRate,
                    'tax_amount' => $taxAmount,
                    'shipping_cost' => $shippingCost,
                    'total' => $total,
                    'shipping_method' => [
                        'service_name' => $rateObj['serviceName'] ?? '',
                        'service_type' => $rateObj['serviceType'] ?? '',
                        'total_net_charges' => $rateObj['totalCharge'] ?? '',
                        'currency' => $rateObj['currency'] ?? '',
                        'total_base_charges' => $rateObj['baseCharge'] ?? '',
                        'fuel_surcharges' => $rateObj['fuelSurcharge'] ?? '',
                        'delivery_surcharges' => $rateObj['deliverySurcharge'] ?? '',
                    ]
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Buy Now totals calculated.',
                'totals' => session('buy_now_totals'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate totals.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function getBuyNowData()
    {
        $item = session('buy_now');

        if (!$item) {
            return response()->json(['error' => 'No Buy Now item'], 404);
        }

        $product = Product::where('slug', $item['product_slug'])->first();

        return response()->json([
            'product' => $product->slug,
            'quantity' => $item['quantity'],
            'price' => $item['price'],
        ]);
    }

    public function clear()
    {
        session()->forget('buy_now');
        return response()->json(['success' => true]);
    }
}
