<?php

namespace App\Http\Controllers\Api;

use App\Models\BuyNow;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\BuyNowResource;

class BuyNowController extends Controller
{
    protected $model;
    protected $modelResource;

    public function __construct(BuyNow $model)
    {
        $this->model = $model; 
        $this->modelResource = new BuyNowResource(null); ; 
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'slug' => 'required|exists:products,slug',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::where('slug', $request->slug)->first();
        if(isset($product) && !empty($product)){
            $conditions = auth()->check()
                ? ['customer_id' => auth()->id()]
                : ['session_id' => $request->guest_id];

            // Delete existing record if it exists
            $this->model->where($conditions)->delete();

            // Now create new record
            $buyNow = $this->model->create([
                ...$conditions,
                'product_slug' => $product->slug,
                'quantity'     => $request->input('quantity', 1),
                'unit_price'   => $product->unit_price,
                'total'        => $product->unit_price * $request->input('quantity', 1),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Buy now retrieved successfully.',
                'buyNow' => new $this->modelResource($buyNow),
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Failed to purchase product'
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function getBuyNowData(Request $request)
    {
        $buyNowCart = $this->model->where(function ($query) use ($request) {
            if (auth()->check()) {
                $query->where('customer_id', auth()->id());
            } elseif ($request->has('guest_id')) {
                $query->where('session_id', $request->guest_id);
            } 
        })->first();

        $product = Product::where('slug', $buyNowCart->product_slug)->first();

        if(!empty($product)){
            return response()->json([
                'success' => true,
                'message' => 'Buy now retrieved successfully.',
                'buyNow' => new $this->modelResource($buyNowCart),
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Record not found',
            ]);
        }
    }

    public function clear(Request $request)
    {
        $buyNow = $this->model->where(function ($query) use ($request) {
            if (auth()->check()) {
                $query->where('customer_id', auth()->id());
            } elseif ($request->has('guest_id')) {
                $query->where('session_id', $request->guest_id);
            } 
        })->first();

        if(isset($buyNow) && !empty($buyNow)){
            $buyNow->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'buy now cleared successfully.',
        ]);
    }
}
