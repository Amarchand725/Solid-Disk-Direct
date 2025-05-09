<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Cart;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Http\Resources\CartItemResource;

class CartController extends Controller
{
    protected $model;
    protected $productModal;
    protected $cartItemModal;
    protected $cartResource;
    protected $cartItemResource;

    public function __construct(Cart $model)
    {
        $this->model = $model; 
        $this->productModal = new Product();
        $this->cartItemModal = new CartItem(); 
        $this->cartResource = new CartResource(null); 
        $this->cartItemResource = new CartItemResource(null); 
    }

    public function getCart()
    {
        $cart = $this->model->with('items.product')
            ->where(function ($query) {
                $query->where('customer_id', auth()->id())
                    ->orWhere('session_id', session()->getId());
            })->first();

        if (!$cart) {
            return response()->json([
                'success' => true,
                'message' => 'Cart is empty.',
                'cart' => [],
                'cart_total' => 0,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Cart retrieved successfully.',
            'cart' => new $this->cartResource($cart),
        ]);
    }

    public function store(Request $request){
        // return $request;
        DB::beginTransaction();

        try {
            $cart = $this->model->where('customer_id', auth()->id())
                ->orWhere('session_id', session()->getId())
                ->first();

            if (!$cart) {
                $cart = $this->model->create([
                    'customer_id' => auth()->check() ? auth()->id() : null, //if user is authenticated
                    'session_id' => auth()->check() ? null : session()->getId(), //if user is guest
                ]);
            }

            $product = $this->productModal->where('slug', $request->slug)->firstOrFail();

            if(!empty($product)){
                $cartItem = $this->cartItemModal->where('cart_id', $cart->id)
                    ->where('product_id', $product->id)
                    ->first();

                if ($cartItem) {
                    $cartItem->quantity += $request->quantity;
                    $cartItem->sub_total = $cartItem->quantity * $product->unit_price;
                    $cartItem->save();
                } else {
                    $this->cartItemModal->create([
                        'cart_id' => $cart->id,
                        'product_id' => $product->id,
                        'quantity' => $request->quantity ?? 1,
                        'unit_price' => $product->unit_price,
                        'sub_total' => $product->unit_price * ($request->quantity ?? 1),
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

    public function clearCart()
    {
        $cart = $this->model->where('customer_id', auth()->id())
            ->orWhere('session_id', session()->getId())
            ->first();

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
}
