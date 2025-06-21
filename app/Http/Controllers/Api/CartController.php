<?php

namespace App\Http\Controllers\Api;

use App\Models\BuyNow;
use Exception;
use App\Models\Cart;
use App\Models\State;
use App\Models\Country;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\OrderShippingMethod;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\BuyNowResource;
use App\Http\Resources\CartResource;
use App\Http\Resources\CartItemResource;
use BcMath\Number;

class CartController extends Controller
{
    protected $model;
    protected $buyNowModel;
    protected $buyNowModelResource;
    protected $productModal;
    protected $cartItemModal;
    protected $orderShippingMethodModal;
    protected $cartResource;
    protected $cartItemResource;

    public function __construct(Cart $model)
    {
        $this->model = $model; 
        $this->buyNowModel = new BuyNow(); 
        $this->productModal = new Product();
        $this->cartItemModal = new CartItem(); 
        $this->orderShippingMethodModal = new OrderShippingMethod(); 
        $this->buyNowModelResource = new BuyNowResource(null); 
        $this->cartResource = new CartResource(null); 
        $this->cartItemResource = new CartItemResource(null); 
    }

    public function getCart(Request $request)
    {
        // $buyNow = $this->buyNowModel->where(function ($query) use ($request) {
        //     if (auth()->check()) {
        //         $query->where('customer_id', auth()->id());
        //     } elseif ($request->has('guest_id')) {
        //         $query->where('session_id', $request->guest_id);
        //     } 
        // })->first();

        // if(isset($buyNow) && !empty($buyNow)){
        //     $buyNow->delete();
        // }

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
            $buyNow = $this->buyNowModel->where(function ($query) use ($request) {
                if (auth()->check()) {
                    $query->where('customer_id', auth()->id());
                } elseif ($request->has('guest_id')) {
                    $query->where('session_id', $request->guest_id);
                } 
            })->first();

            if(isset($buyNow) && !empty($buyNow)){
                $buyNow->delete();
            }

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
            $shippingWeight = 0;

            if ($product->discount_price) {
                $productPrice = $product->discount_price;
            } elseif ($product->unit_price) {
                $productPrice = $product->unit_price;
            }

            if(!empty($product)){
                $cartItem = $this->cartItemModal->where('cart_id', $cart->id)
                    ->where('product_id', $product->id)
                    ->first();

                $shippingWeight = getWeightOnlyAttribute($product->shipping_weight)*$request->quantity;
                if ($cartItem) {
                    $cartItem->shipping_weight = $shippingWeight;
                    $cartItem->quantity += $request->quantity;
                    $cartItem->sub_total = $cartItem->quantity * $productPrice;
                    $cartItem->save();
                } else {
                    $this->cartItemModal->create([
                        'cart_id' => $cart->id,
                        'product_id' => $product->id,
                        'shipping_weight' => $shippingWeight ?? 0,
                        'quantity' => $request->quantity ?? 1,
                        'unit_price' => $productPrice,
                        'sub_total' => round($productPrice * ($request->quantity ?? 1), 2),
                        'options' => $request->options ? json_encode($request->options) : null,
                    ]);
                }

                $cart->update([
                    'shipping_weight' => $cart->items->sum(fn($item) => $item->quantity * $item->shipping_weight),
                    'subtotal' => round($cart->items->sum(fn($item) => $item->quantity * $item->unit_price), 2),
                    'total' => round($cart->items->sum(fn($item) => $item->quantity * $item->unit_price), 2),
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
        $productShippingWeight = getProductShippingWeight($cartItem->product_id);
        // Optional: Check stock here
        $totQty = $cartItem->quantity + 1;
        $cartItem->quantity += 1;
        $cartItem->shipping_weight = $productShippingWeight*$totQty;
        $cartItem->sub_total = $cartItem->quantity * $cartItem->unit_price;
        $cartItem->save();

        // Update cart total
        $cartItem->cart->update([
            'shipping_weight' => $cartItem->cart->items->sum(fn($item) => $item->shipping_weight),
            'subtotal' => round($cartItem->cart->items->sum(fn($item) => $item->sub_total), 2),
            'total' => round($cartItem->cart->items->sum(fn($item) => $item->sub_total), 2),
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
        $productShippingWeight = getProductShippingWeight($cartItem->product_id);
        // Optional: Check stock here
        
        if ($cartItem->quantity > 1) {
            $totQty = $cartItem->quantity - 1;

            $cartItem->quantity -= 1;
            $cartItem->sub_total = round($cartItem->quantity * $cartItem->unit_price, 2);
            $cartItem->shipping_weight = $productShippingWeight*$totQty;
            $cartItem->save();
        } else {
            // Optional: Remove item if quantity is 1
            $cartItem->delete();
        }

        

        // Update cart total
        $cart = $cartItem->cart;
        $cart->update([
            'shipping_weight' => $cartItem->cart->items->sum(fn($item) => $item->shipping_weight),
            'subtotal' => round($cart->items->sum(fn($item) => $item->sub_total), 2),
            'total' => round($cart->items->sum(fn($item) => $item->sub_total), 2),
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
            'shipping_weight' => $cart->items->sum(fn($item) => $item->shipping_weight),
            'subtotal' => round($cart->items->sum(fn($item) => $item->sub_total), 2),
            'total' => round($cart->items->sum(fn($item) => $item->sub_total), 2),
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
            $buyNowCart = $this->buyNowModel->where(function ($query) use ($request) {
                if (auth()->check()) {
                    $query->where('customer_id', auth()->id());
                } elseif ($request->has('guest_id')) {
                    $query->where('session_id', $request->guest_id);
                } 
            })->first();

            if(isset($buyNowCart) && !empty($buyNowCart)){
                $shippingCost = $rateObj['totalCharge'] ?? 0;

                $subtotal = $buyNowCart->unit_price*$buyNowCart->quantity;

                $total = round($subtotal + $shippingCost, 2);

                $buyNowCart->shipping_cost = $shippingCost;
                $buyNowCart->subtotal = $subtotal;
                $buyNowCart->total = $total;
                $buyNowCart->save();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Buy now retrieved successfully.',
                    'isBuyNow' => true,
                    'buyNow' => new $this->buyNowModelResource($buyNowCart),
                ]);
            }else{
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

                $subtotal = $cart->items->sum(function ($item) {
                    return $item->unit_price * $item->quantity;
                });

                $total = round($subtotal + $shippingCost, 2);

                $cart->shipping_cost = $shippingCost;
                $cart->total = $total;
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
            'country' => 'required',
            'state' => 'nullable',
            'guest_id' => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            $buyNowCart = $this->buyNowModel->where(function ($query) use ($request) {
                if (auth()->check()) {
                    $query->where('customer_id', auth()->id());
                } elseif ($request->has('guest_id')) {
                    $query->where('session_id', $request->guest_id);
                } 
            })->first();

            if(isset($buyNowCart) && !empty($buyNowCart)){
                $country = $request->input('country');
                $state = $request->input('state');

                $taxInfo = $this->getTaxInfo($country, $state); // e.g. 7.5 for 7.5%
                $taxRate = isset($taxInfo['rate']) ? (float)$taxInfo['rate'] : 0.0;
                $subtotal = $buyNowCart->unit_price*$buyNowCart->quantity;
                $shippingCost = $buyNowCart->shipping_cost ?? 0;

                $taxAmount = round(($taxRate / 100) * $subtotal, 2);
                $total = round($subtotal + $shippingCost + $taxAmount, 2);

                $buyNowCart->tax_rate = $taxRate;
                $buyNowCart->tax_amount = $taxAmount;
                $buyNowCart->subtotal = round($subtotal, 2);
                $buyNowCart->total = round($total, 2);
                $buyNowCart->save();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Buy now retrieved successfully.',
                    'isBuyNow' => true,
                    'buyNow' => new $this->buyNowModelResource($buyNowCart),
                ], 200);
            }else{
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
                        'message' => 'Cart not found.',
                    ], 404);
                }

                $country = $request->input('country');
                $state = $request->input('state');

                $taxInfo = $this->getTaxInfo($country, $state); // e.g. 7.5 for 7.5%
                $taxRate = isset($taxInfo['rate']) ? (float)$taxInfo['rate'] : 0.0;
                $subtotal = $cart->subtotal ?? $cart->items->sum(fn ($item) => $item->unit_price * $item->quantity);
                $shippingCost = $cart->shipping_cost ?? 0;

                $taxAmount = round(($taxRate / 100) * $subtotal, 2);
                $total = round($subtotal + $shippingCost + $taxAmount, 2);

                $cart->tax_rate = round($taxRate, 2);
                $cart->tax_amount = round($taxAmount, 2);
                $cart->total = round($total, 2);
                $cart->save();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Tax updated.',
                    'cart' => new $this->cartResource($cart)
                ]);
            }
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update tax.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    protected function getTaxInfo($countryId, $stateId = null)
    {
        $country = Country::where('id', $countryId)->first();
        $countryCode = $country->code;
        if (!$country) {
            return ['rate' => 0, 'percent' => null];
        }

        if (strtoupper($countryCode) === 'US' && $stateId) {
            $state = State::where('id', $stateId)
                ->where('country_id', $country->id)
                ->first();

            if ($state && $state->percent !== null) {
                return [
                    'rate' => $state->rate ?? 0,
                    'percent' => $state->percent ?? null
                ];
            }
        }
        
        return [
            'rate' => $country->rate ?? 0,
            'percent' => $country->percent ?? null
        ];
    }
    public function clearCharges(Request $request)
    {
        $request->validate([
            'guest_id' => 'nullable|string'
        ]);

        DB::beginTransaction(); 

        try {
            // --- Handle Buy Now ---
            $buyNowCart = $this->buyNowModel->where(function ($query) use ($request) {
                if (auth()->check()) {
                    $query->where('customer_id', auth()->id());
                } elseif ($request->has('guest_id')) {
                    $query->where('session_id', $request->guest_id);
                }
            })->first();

            if ($buyNowCart) {
                $buyNowCart->shipping_cost = 0.00;
                $buyNowCart->tax_rate = 0.00;
                $buyNowCart->tax_amount = 0.00;
                $buyNowCart->subtotal = round($buyNowCart->unit_price * $buyNowCart->quantity, 2);
                $buyNowCart->total = round($buyNowCart->subtotal, 2); // No shipping/tax

                $buyNowCart->save();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Buy now charges cleared.',
                    'isBuyNow' => true,
                    'buyNow' => new $this->buyNowModelResource($buyNowCart),
                ]);
            }

            // --- Handle Normal Cart ---
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
                    'message' => 'Cart not found.',
                ], 404);
            }

            $subtotal = $cart->items->sum(fn($item) => $item->unit_price * $item->quantity);

            $cart->shipping_cost = 0.00;
            $cart->tax_rate = 0.00;
            $cart->tax_amount = 0.00;
            $cart->subtotal = round($subtotal, 2);
            $cart->total = round($subtotal, 2); // No shipping/tax

            $cart->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cart charges cleared.',
                'cart' => new $this->cartResource($cart),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cart charges.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
