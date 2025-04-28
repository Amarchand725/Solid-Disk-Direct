<?php

namespace App\Http\Controllers\frontend;

use Exception;
use App\Models\Cart;
use App\Models\User;
use App\Models\Order;
use App\Models\CartItem;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Models\BillingAddress;
use App\Models\ShippingAddress;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Notifications\SiteEventNotification;

class OrderController extends Controller
{
    public function index(){

    }

    public function placeOrder(Request $request)
    {
        if(!session()->has('checkout_data')){
            // Define the validation rules
            $rules = [
                'payment_method' => 'required|exists:payment_methods,id',
                'shipping.email' => 'required|email',
                'shipping.first_name' => 'required|string',
                'shipping.country_id' => 'required|exists:countries,id',
                'shipping.address1' => 'required|string',
                'shipping.zip' => 'required|string',
                'shipping.city' => 'required|string',
                'shipping.phone' => 'required|string',
            ];

            // If the billing address is not the same as the shipping address, add validation for billing fields
            if (!$request->has('same_as_shipping')) {
                $rules = array_merge($rules, [
                    'billing.email' => 'required|email',
                    'billing.first_name' => 'required|string',
                    'billing.country_id' => 'required|exists:countries,id',
                    'billing.address1' => 'required|string',
                    'billing.zip' => 'required|string',
                    'billing.city' => 'required|string',
                    'billing.phone' => 'required|string',
                ]);
            }

            // Validate the request data
            $validatedData = $request->validate($rules);
        }
        
        // If not logged in — store raw data and redirect to login
        if (!Auth::guard('customer')->check()) {
            session()->put('checkout_data', $request->all());
            session()->put('validatedData', $validatedData);
            session()->put('guest_cart_id', session()->getId());
            return redirect()->route('customer.login');
        }
        
        $request = session()->has('checkout_data') ? session()->pull('checkout_data') : $request->all();
        $validatedData = session()->has('validatedData') ? session()->pull('validatedData') : $validatedData;
        
        // Attach guest cart to user
        if(session()->has('checkout_data') && Auth::guard('customer')->check()) {
            $this->attachCartToUser();
        }

        DB::beginTransaction();
        try{
            $customerId = Auth::guard('customer')->id();
            $orderPlaced = false;
            // Create a new order
            $order = new Order();
            $order->customer_id = $customerId; // Or pass customer id if logged in
            $order->order_number = 'ORD' . strtoupper(uniqid()); // Generate unique order number
            $order->sub_total = getCartData()['total']; // Assuming you have a cart data helper function to get the cart total
            $order->total = $order->sub_total + $request['shipping_charges'] + $request['tax']; // Calculate total
            $order->payment_status = 'pending'; // Default status
            $order->order_status = 'pending'; // Default status
            $order->payment_method = $validatedData['payment_method'];
            $order->additional_note = $validatedData['notes'] ?? null; // Optional notes
            $order->save();
            
            if(isset($order) && !empty($order)) {
                // Insert order items
                foreach (getCartData()['items'] ?? [] as $item) {
                    $orderItem = new OrderItem();
                    $orderItem->order_id = $order->id;
                    $orderItem->product_id = $item['product']['id'];
                    $orderItem->price = $item['product']['price'];
                    $orderItem->quantity = $item['quantity'];
                    $orderItem->sub_total = $item['sub_total'];
                    $orderItem->save();
                }

                // Create Shipping Address
                $shippingAddress = new ShippingAddress();
                $shippingAddress->customer_id = $customerId; // Or pass user ID if logged in
                $shippingAddress->email = $validatedData['shipping']['email'] ?? null;
                $shippingAddress->first_name = $validatedData['shipping']['first_name'] ?? null;
                $shippingAddress->last_name = $validatedData['shipping']['last_name'] ?? null;
                $shippingAddress->country_id = $validatedData['shipping']['country_id'] ?? null;
                $shippingAddress->address1 = $validatedData['shipping']['address1'] ?? null;
                $shippingAddress->address2 = $validatedData['shipping']['address2'] ?? null;
                $shippingAddress->zip = $validatedData['shipping']['zip'] ?? null;
                $shippingAddress->city = $validatedData['shipping']['city'] ?? null;
                $shippingAddress->phone = $validatedData['shipping']['phone'] ?? null;
                $shippingAddress->save();

                // Assign the shipping address ID to the order
                $order->shipping_address_id = $shippingAddress->id;
                $order->save();
                
                // Create Billing Address if different
                if (!isset($request['same_as_shipping'])) { //2=means billing address is change not same as shipping.
                    $billingAddress = new BillingAddress(); // Assuming you have an Address model for billing
                    $billingAddress->customer_id = $customerId; // Or pass user ID if logged in
                    $billingAddress->email = $validatedData['billing']['email'] ?? null;
                    $billingAddress->first_name = $validatedData['billing']['first_name'] ?? null;
                    $billingAddress->last_name = $validatedData['billing']['last_name'] ?? null;
                    $billingAddress->country_id = $validatedData['billing']['country_id'] ?? null;
                    $billingAddress->address1 = $validatedData['billing']['address1'] ?? null;
                    $billingAddress->address2 = $validatedData['billing']['address2'] ?? null;
                    $billingAddress->zip = $validatedData['billing']['zip'] ?? null;
                    $billingAddress->city = $validatedData['billing']['city'] ?? null;
                    $billingAddress->phone = $validatedData['billing']['phone'] ?? null;
                    $billingAddress->save();

                    // Assign the billing address ID to the order
                    $order->billing_address_id = $billingAddress->id;
                    $order->save();
                }

                $orderPlaced = true;
            }

            if($orderPlaced){
                DB::commit();

                if(isset(getCartData()['cart_token']) && !empty(getCartData()['cart_token'])){
                    // Retrieve the cart token from session or wherever it's stored
                    $cartToken = getCartData()['cart_token']; // Assuming you have this function returning the cart token

                    // Step 1: Delete all cart items associated with the cart
                    CartItem::where('cart_token', $cartToken)->delete();

                    // Step 2: Delete the cart itself
                    Cart::where('cart_token', $cartToken)->delete();
                }

                // Clear session data related to the checkout process
                session()->forget('checkout_data');
                session()->forget('validatedData');
                session()->forget('guest_cart_id');

                //it is event notification
                $admin = getActiveAdminUser();
                if(!empty($admin)){
                    $url = route('orders.index');
                    $admin->notify(new SiteEventNotification('shopping-cart.png','New Order', "Order #{$order->order_number} has been placed.", $url));
                }
                //it is event notification
                
                // Return the order confirmation page or redirect as needed
                return redirect()->route('order.success')->with(['success' => true, 'order_number' => $order->order_number, 'message' => 'You placed order successfully.']);
            }else{
                DB::rollback();
                return redirect()->back()->with(['error' => false, 'message' => 'Something went wrong try again']);
            }
        }catch(Exception $e){
            DB::rollback();
            // Return the order confirmation page or redirect as needed
            return redirect()->back()->with(['error' => false, 'message' => $e->getMessage()]);
        }
    }

    protected function attachCartToUser()
    {
        $guestCartId = session()->pull('guest_cart_id');
        $userId = Auth::guard('customer')->id();

        if (!$guestCartId || !$userId) return;

        // Example: update `carts` table to change owner
        DB::table('carts')
            ->where('session_id', $guestCartId)
            ->update([
                'user_id' => $userId,
                'session_id' => null, // optional cleanup
            ]);
    }


    public function orderSuccess()
    {
        // Retrieve flash data
        $orderStatus = session('success');
        $orderNumber = session('order_number');
        $message = session('message');

        return view('frontend.cart.success', compact('orderStatus', 'orderNumber', 'message'));
    }
}
