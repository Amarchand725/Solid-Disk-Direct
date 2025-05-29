<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Cart;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\OrderShippingMethod;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Http\Resources\CartItemResource;

class CartController extends Controller
{
    protected $model;
    protected $productModal;
    protected $cartItemModal;
    protected $orderShippingMethodModal;
    protected $cartResource;
    protected $cartItemResource;

    public function __construct(Cart $model)
    {
        $this->model = $model; 
        $this->productModal = new Product();
        $this->cartItemModal = new CartItem(); 
        $this->orderShippingMethodModal = new OrderShippingMethod(); 
        $this->cartResource = new CartResource(null); 
        $this->cartItemResource = new CartItemResource(null); 
    }

    public function getCart(Request $request)
    {
        // $cart = $this->model->where('customer_id', auth()->id())
        //         ->orWhere('session_id', session()->getId())
        //         ->first();

        $cart = $this->model->where(function ($query) use ($request) {
            if (auth()->check()) {
                $query->where('customer_id', auth()->id());
            } elseif ($request->has('guest_id')) {
                $query->where('session_id', $request->guest_id);
            } 
        })->first();

        if (!$cart) {
            return response()->json([
                'success' => true,
                'message' => 'Cart is empty.',
                'cart' => [],
                'cart_total' => 0,
            ]);
        }

        $cartTotal = $cart->items->sum(function ($item) {
            return $item->product ? $item->quantity * $item->product->price : 0;
        });

        return response()->json([
            'success' => true,
            'message' => 'Cart retrieved successfully.',
            'cart' => new $this->cartResource($cart),
            'cart_total' => $cartTotal,
        ]);
    }

    public function store(Request $request){
        DB::beginTransaction();

        try {
            $cart = $this->model->where(function ($query) use ($request) {
                if (auth()->check()) {
                    $query->where('customer_id', auth()->id());
                } elseif ($request->has('guest_id')) {
                    $query->where('session_id', $request->guest_id);
                } 
            })->first();

            if (!$cart) {
                $cart = $this->model->create([
                    'customer_id' => auth()->check() ? auth()->id() : null, //if user is authenticated
                    'session_id' => auth()->check() ? null : ($request->guest_id ?? null), //if user is guest
                ]);
            }

            $product = $this->productModal->where('slug', $request->slug)->firstOrFail();

            $productPrice = 0;

            if ($product->discount_price) {
                $productPrice = $product->discount_price;
            } elseif ($product->unit_price) {
                $productPrice = $product->unit_price;
            }

            if(!empty($product)){
                $cartItem = $this->cartItemModal->where('cart_id', $cart->id)
                    ->where('product_id', $product->id)
                    ->first();

                if ($cartItem) {
                    $cartItem->quantity += $request->quantity;
                    $cartItem->sub_total = $cartItem->quantity * $productPrice;
                    $cartItem->save();
                } else {
                    $this->cartItemModal->create([
                        'cart_id' => $cart->id,
                        'product_id' => $product->id,
                        'quantity' => $request->quantity ?? 1,
                        'unit_price' => $productPrice,
                        'sub_total' => $productPrice * ($request->quantity ?? 1),
                        'options' => $request->options ? json_encode($request->options) : null,
                    ]);
                }

                $cart->update([
                    'subtotal' => $cart->items->sum(fn($item) => $item->quantity * $item->unit_price),
                    'total' => $cart->items->sum(fn($item) => $item->quantity * $item->unit_price),
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Item added successfully.!',
                    'items' => $cart->items->count(),
                    'cart' => new $this->cartResource($cart->fresh('items'))
                ]);
            }else{
                DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found.!'
                ]);
            }
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ]);
        }
    }
    public function increaseQuantity(Request $request)
    {
        $request->validate([
            'cart_item_id' => 'required|exists:cart_items,id',
        ]);

        $cartItem = $this->cartItemModal->findOrFail($request->cart_item_id);

        // Optional: Check stock here
        $cartItem->quantity += 1;
        $cartItem->sub_total = $cartItem->quantity * $cartItem->unit_price;
        $cartItem->save();

        // Update cart total
        $cartItem->cart->update([
            'subtotal' => $cartItem->cart->items->sum(fn($item) => $item->sub_total),
            'total' => $cartItem->cart->items->sum(fn($item) => $item->sub_total),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Quantity increased.!',
            'items' => $cartItem->fresh()->quantity,
            'cart' => new $this->cartResource($cartItem->cart)
        ]);
    }

    public function decreaseQuantity(Request $request)
    {
        $request->validate([
            'cart_item_id' => 'required|exists:cart_items,id',
        ]);

        $cartItem = $this->cartItemModal->findOrFail($request->cart_item_id);

        if ($cartItem->quantity > 1) {
            $cartItem->quantity -= 1;
            $cartItem->sub_total = $cartItem->quantity * $cartItem->unit_price;
            $cartItem->save();
        } else {
            // Optional: Remove item if quantity is 1
            $cartItem->delete();
        }

        // Update cart total
        $cart = $cartItem->cart;
        $cart->update([
            'subtotal' => $cart->items->sum(fn($item) => $item->sub_total),
            'total' => $cart->items->sum(fn($item) => $item->sub_total),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Quantity decreased.!',
            'items' => $cartItem->fresh()->quantity,
            'cart' => new $this->cartResource($cartItem->cart)
        ]);
    }

    public function removeItem(Request $request)
    {
        $request->validate([
            'cart_item_id' => 'required|exists:cart_items,id',
        ]);
        
        $cartItem = $this->cartItemModal->findOrFail($request->cart_item_id);
        $cart = $cartItem->cart;

        $cartItem->delete();

        // Recalculate cart total after deletion
        $cart->update([
            'subtotal' => $cart->items->sum(fn($item) => $item->sub_total),
            'total' => $cart->items->sum(fn($item) => $item->sub_total),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart.',
            'items' => $cart->items->count(),
            'cart' => new $this->cartResource($cart->fresh('items'))
        ]);
    }
    public function clearCart(Request $request)
    {
        $cart = $this->model->where(function ($query) use ($request) {
            if (auth()->check()) {
                $query->where('customer_id', auth()->id());
            } elseif ($request->has('guest_id')) {
                $query->where('session_id', $request->guest_id);
            } 
        })->first();

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'No cart found to clear.',
            ]);
        }

        // Delete all cart items
        $cart->items()->delete();

        // Reset cart total
        $cart->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully.',
        ]);
    }
    public function updateShipping(Request $request){
        $rateObj = $request->rate;
        DB::beginTransaction();

        try {
            $cart = $this->model->where(function ($query) use ($request) {
                if (auth()->check()) {
                    $query->where('customer_id', auth()->id());
                } elseif ($request->has('guest_id')) {
                    $query->where('session_id', $request->guest_id);
                } 
            })->first();

            if (!$cart) {
                $cart = $this->model->create([
                    'customer_id' => auth()->check() ? auth()->id() : null, //if user is authenticated
                    'session_id' => auth()->check() ? null : ($request->guest_id ?? null), //if user is guest
                ]);
            }

            // Shipping cost
            $shippingCost = $rateObj['totalCharge'] ?? 0;

            // Subtotal (must exclude shipping and tax; implement calculateSubtotal())
            // $taxRate = $this->getTaxRate($country, $state);

            $cart->shipping_cost = $shippingCost;
            // $cart->shipping_cost = $shippingCost;
            $cart->total = $cart->total+$rateObj['totalCharge'] ?? 0;
            $cart->save();

            if($cart){
                $this->orderShippingMethodModal->updateOrCreate(
        ['cart_id' => $cart->id], // Condition to check
            [
                        'service_name' => $rateObj['serviceName'] ?? '',
                        'service_type' => $rateObj['serviceType'] ?? '',
                        'total_net_charges' => $rateObj['totalCharge'] ?? '',
                        'currency' => $rateObj['currency'] ?? '',
                        'total_base_charges' => $rateObj['baseCharge'] ?? '',
                        'fuel_surcharges' => $rateObj['fuelSurcharge'] ?? '',
                        'delivery_surcharges' => $rateObj['deliverySurcharge'] ?? '',
                    ]
                );

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'You selected shipping method.!',
                    'cart' => new $this->cartResource($cart)
                ]);
            }else{
                DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => 'Something went wrong.!'
                ]);
            }
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ]);
        }
    }
    public function updateTax(Request $request)
    {
        $request->validate([
            'country' => 'required|string',
            'state' => 'nullable|string',
        ]);

        $cart = Cart::getCurrent(); // Or your method for guest/user cart
        $country = $request->input('country');
        $state = $request->input('state');

        $taxRate = $this->getTaxRate($country, $state);
        $cartSubtotal = $cart->calculateSubtotal(); // Make sure you have this logic

        $taxAmount = round(($taxRate / 100) * $cartSubtotal, 2);

        $cart->update([
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'total' => $cartSubtotal + $taxAmount + $cart->shipping_cost, // Update accordingly
        ]);

        return response()->json([
            'message' => 'Tax updated.',
            'cart' => $cart->fresh()
        ]);
    }
}
