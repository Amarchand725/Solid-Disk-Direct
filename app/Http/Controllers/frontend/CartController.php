<?php

namespace App\Http\Controllers\frontend;

use Exception;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\Product;

class CartController extends Controller
{
    public function cart(){
        $title = "Cart";
        return view('frontend.cart.cart', get_defined_vars());
    }
    public function checkout(){
        $title = "Checkout";
        $paymentMethods = PaymentMethod::where("status",1)->get(['id', 'name']);
        return view('frontend.cart.checkout', get_defined_vars());
    }

    // Add product to cart
    public function addToCart(Request $request)
    {
        DB::beginTransaction();

        try{
            $cartExist = Cart::where('user_id', auth()->id())->orWhere('guest_id', session()->getId())->first();

            if(!$cartExist){
                $cartToken = 'cart-' . rand(100000, 999999);
                $cart = Cart::firstOrCreate([
                    'cart_token' => $cartToken,
                    'user_id' => auth()->id(),
                    'guest_id' => auth()->check() ? null : session()->getId(),
                ]);
            }else{
                $cart = $cartExist;
            }
            
            $product = Product::where('provider_product_id', $request->slug)->first();
            $product_id = null;
            $price = 0;
            if(isset($product) && !empty($product)){
                $product_id = $product->id;
                $price = $product->sale_price;
            }

            $productExist = CartItem::where('cart_token', $cart->cart_token)->where('product_id', $product_id)->first();
            if($productExist){
                $quantity = $productExist->quantity+1;
                $productExist->quantity = $quantity;
                $productExist->sub_total = $quantity*$product->sale_price;
                $productExist->save();

                // Update the total cart value
                $cartItem = $cart->update([
                    'total' => $cart->items->sum(function ($item) {
                        return $item->quantity * $item->price;
                    }),
                ]);
            }else{
                if(isset($cart) && !empty($cart)){
                    CartItem::create([
                        'cart_token' => $cart->cart_token,
                        'product_id' => $product_id,
                        'quantity' => $request->quantity,
                        'unit_price' => $price,
                        'sub_total' => $price * $request->quantity,
                        'options' => isset($request->options) ? json_encode($request->options) : null,
                    ]);
                    
                    // Update the total cart value
                    $cartItem = $cart->update([
                        'total' => $cart->items->sum(function ($item) {
                            return $item->quantity * $item->unit_price;
                        }),
                    ]);
                };
            }
            if($cartItem){
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Item added to cart!',
                    'items' => count($cart->items),
                    'cart' => $cart
                ]);
            }else{
                DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => 'Item not added to cart try again!',
                ]);
            }
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    // Update cart item quantity
    public function updateCartItem(Request $request)
    {
        DB::beginTransaction();

        try{
            $cartItem = CartItem::find($request->cartId); // Find the cart item

            if (!$cartItem) {
                return response()->json(['success' => false, 'message' => 'Cart item not found'], 404);
            }

            // Update quantity
            $cartItem->quantity = $request->quantity;
            $cartItem->sub_total = $cartItem->quantity * $cartItem->unit_price;
            $cartItem->save();

            // Recalculate totals
            $cart = Cart::where('cart_token', $cartItem->cart_token)->first();
            $cart->update([
                'total' => $cart->items->sum(function ($item) {
                    return $item->quantity * $item->unit_price;
                }),
            ]);

            

            if($cart){
                DB::commit();
                return response()->json([
                    'success' => true,
                    'item_sub_total' => number_format($cartItem->sub_total, 2),
                    'sub_total' => number_format($cart->items->sum('sub_total'), 2),
                    'total' => number_format($cart->total, 2),
                    'items' => $cart->items->count(),
                ]);
            }else{
                DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => 'Cart item not updated!'
                ]);
            }
        }catch (Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    // Remove item from cart
    public function removeCartItem(Request $request, $cartToken)
    {
        DB::beginTransaction();

        try{
            $cartItem = CartItem::where('cart_token', $cartToken)->where('id', $request->cartId)->first();
            if(isset($cartItem) && !empty($cartItem)){
                $cartItem->delete();
            }

            $cart = Cart::where('cart_token', $cartToken)->first();
            // Update the total cart value
            $cartUpdated = $cart->update([
                'total' => $cart->items->sum(function ($item) {
                    return $item->quantity * $item->unit_price;
                }),
            ]);

            if($cartUpdated){
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Cart item removed!',
                    'items' => count($cart->items),
                    'sub_total' => number_format($cart->items->sum('sub_total'), 2),
                    'total' => number_format($cart->total, 2),
                ]);
            }else{
                DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => 'Cart item not removed!'
                ]);
            }
        }catch (Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    // Clear the cart
    public function clearCart()
    {
        DB::beginTransaction();

        try{
            $cart = Cart::where('user_id', auth()->id())
                ->orWhere('guest_id', session()->getId())
                ->first();

            if ($cart) {
                $cart->items()->delete(); // Delete all items
                $cart->delete(); // Delete cart itself

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Cart cleared!'
                ]);
            }else{
                DB::rollback();
                return response()->json([
                    'success' => true,
                    'message' => 'Cart cleared!'
                ]);
            }
        }catch (Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
