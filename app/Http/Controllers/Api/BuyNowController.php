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
                    'quantity' => $request->quantity,
                    'price' => $product->unit_price, // snapshot
                ],
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Failed to purchase product'
            ]);
        }

        return response()->json(['success' => true]);
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
