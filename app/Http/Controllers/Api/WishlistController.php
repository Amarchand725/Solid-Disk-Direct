<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\Wishlist;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\WishlistResource;

class WishlistController extends Controller
{
    use ApiResponse;
    protected $model;
    protected $productModel;

    public function __construct(Wishlist $model)
    {
        $this->model = $model; 
        $this->productModel = new Product();
    }
    public function getWishlist()
    {
        $wishlistItems = $this->model->with('hasCustomer', 'hasProducts') // eager load product relation
            ->where('customer', 1)
            ->get();

        return response()->json([
            'success' => true,
            'wishlist' => WishlistResource::collection($wishlistItems),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'slug' => 'required|exists:products,slug',
        ]);

        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to add to your wishlist.',
            ], 401); // 401 Unauthorized
        }        

        $customerId = auth()->id();
        
        // Check if already in wishlist
        $product = $this->productModel->where('slug', $request->slug)->firstOrFail();
        if(!empty($product)){
            $exists = $this->model->where('customer', $customerId)
                ->where('product', $product->id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product is already in your wishlist.',
                ]);
            }

            $this->model->create([
                'customer' => $customerId,
                'product' => $product->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product added to your wishlist.',
            ]);
        }else{
            return $this->error(
                'Product not found.',
                500,
            );
        }
    }
    public function removeFromWishlist(Request $request)
    {
        $request->validate([
            'slug' => 'required|exists:products,slug',
        ]);

        $product = $this->productModel->where('slug', $request->slug)->firstOrFail();
        if(!empty($product)){
            $deleted = $this->model->where('customer', auth()->id())
                ->where('product', $product->id)
                ->delete();

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product removed from wishlist.',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Product not found in your wishlist.',
            ]);
        }else{
            return $this->error(
                'Product not found.',
                500,
            );
        }
    }
}
