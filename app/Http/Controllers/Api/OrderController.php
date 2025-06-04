<?php

namespace App\Http\Controllers\Api;

use Exception;
use Stripe\Stripe;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Mail\OrderConfirmedAdmin;
use Illuminate\Support\Facades\DB;
use App\Models\OrderBillingAddress;
use App\Models\OrderShippingMethod;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Mail\OrderConfirmedCustomer;
use App\Models\OrderShippingAddress;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\PlaceOrderRequest;
use App\Services\Payment\PaymentService;
use Illuminate\Support\Facades\Validator;
use App\Notifications\SiteEventNotification;
use App\Models\{Cart, ShippingMethod, Order};
use App\Services\FedExShippingService;

class OrderController extends Controller
{
    protected $paymentService;
    protected $orderModel;
    protected $orderItemModel;
    protected $productModel;
    protected $orderShippingAddress;
    protected $orderBillingAddress;
    protected $orderShippingService;
    protected $cartModel;
    protected $cartItemsModel;

    public function __construct()
    {
        $this->cartModel = new Cart();
        $this->cartItemsModel = new CartItem();
        $this->orderModel = new Order();
        $this->orderItemModel = new OrderItem();
        $this->productModel = new Product();
        $this->orderShippingAddress = new OrderShippingAddress();
        $this->orderBillingAddress = new OrderBillingAddress();
        $this->orderShippingService = new OrderShippingMethod();
    }

    public function store(Request $request, PlaceOrderRequest $requestValidated)
    {
        $validated = $requestValidated->validated();
        $payment = $request->payment;
        $shipping = $validated['shipping'];
        $billing = $validated['billing'];
        $sameAsShipping = $billing['same_as_shipping'];
        $cart = $request->cart;
        $cartItems = $cart['items'];
        $order_shipping_service = $this->orderShippingService->where('cart_id', $cart['id'])->first();
        
        DB::beginTransaction();
        try {
            $order = $this->orderModel;
            $order->order_number = 'ORD' . strtoupper(substr(bin2hex(random_bytes(2)), 0, 5));
            $order->customer_id = auth()->check() ? auth()->id() : null;
            $order->session_id = auth()->check() ? null : $cart['session_id'];
            $order->coupon_id = NULL;
            $order->same_as_shipping = $sameAsShipping;
            $order->subtotal = $cart['subtotal'] ?? 0;
            $order->tax = $cart['tax_amount'] ?? 0;
            $order->shipping_cost = $cart['shipping_cost'] ?? 0;
            // $order->discount = null;
            $order->total = $cart['total'] ?? 0;
            $order->payment_method = $payment['method'];
            $order->payment_status = 'unpaid';
            $order->additional_note = null;
            $order->save();

            Log::info('Order Added Successfully: '.json_encode($order));

            if($order && $cartItems){
                foreach($cartItems as $item){
                    $product = $this->productModel->where('slug', $item['product']['slug'])->first();
                    if(!empty($product)){
                        $order_item = $this->orderItemModel;
                        $order_item->order_id = $order->id;
                        $order_item->product_id = $product->id;
                        $order_item->variant_id = null;
                        $order_item->unit_price = $item['unit_price'] ?? 0;
                        $order_item->discount = null;
                        $order_item->quantity = $item['quantity'] ?? 0;
                        $order_item->options = null;
                        $order_item->sub_total = $item['sub_total'] ?? 0;
                        $order_item->save();
                    }
                }

                Log::info('Order Item Added Successfully: '.json_encode($order_item));

                if(isset($shipping) && !empty($shipping)){
                    $order_shipping_address = $this->orderShippingAddress;
                    $order_shipping_address->order_id = $order->id;
                    $order_shipping_address->first_name = $shipping['first_name'];
                    $order_shipping_address->last_name = $shipping['last_name'];
                    $order_shipping_address->email = $shipping['email'];
                    $order_shipping_address->phone = $shipping['phone'];
                    $order_shipping_address->address = $shipping['address'];
                    $order_shipping_address->city = $shipping['shippingCity'];
                    $order_shipping_address->state = $shipping['shippingState'];
                    $order_shipping_address->zip = $shipping['zip'];
                    $order_shipping_address->country = $shipping['shippingCountry'];
                    $order_shipping_address->save();

                    Log::info('Order Shipping Address Added Successfully');
                }

                if(isset($sameAsShipping) && $sameAsShipping==true && !empty($billing)){
                    $order_billing_address = $this->orderBillingAddress;  
                    $order_billing_address->order_id = $order->id;
                    $order_billing_address->first_name = $shipping['first_name'];
                    $order_billing_address->last_name = $shipping['last_name'];
                    $order_billing_address->email = $shipping['email'];
                    $order_billing_address->phone = $shipping['phone'];
                    $order_billing_address->address = $shipping['address'];
                    $order_billing_address->city = $shipping['billingCity'];
                    $order_billing_address->state = $shipping['billingState'];
                    $order_billing_address->zip = $shipping['zip'];
                    $order_billing_address->country = $shipping['billingCountry'];
                    $order_billing_address->save();

                    Log::info('Order Billing Address Added Successfully');
                }

                $order_shipping_service = $this->orderShippingService->where('cart_id', $cart['id'])->first();
                if(isset($order_shipping_service) && !empty($order_shipping_service)){
                    $order_shipping_service->order_id = $order->id;
                    $order_shipping_service->save();

                    Log::info('Order Shipping Service updated Successfully');
                }

                //paypal 
                if ($payment['method'] == 'paypal') {
                    DB::commit();
                    $paymentService = new PaymentService('paypal');
                    $paypalResponse = $paymentService->createPaypalOrder($order); 

                    // Save PayPal order ID
                    $order->paypal_order_id = $paypalResponse['id'];
                    $order->save();

                    $cart = $this->cartModel->find($cart['id']);
                    if ($cart) {
                        $cart->items()->delete();
                        $cart->delete();

                        Log::info('Cart and cart item deleted successfully. ');
                    }

                    $approveLink = collect($paypalResponse['links'])->firstWhere('rel', 'approve')['href'];
                    return response()->json([
                        'redirect_url' => $approveLink,
                        'success' => true,
                        'message' => 'Redirecting to PayPal for payment',
                    ]);
                }elseif ($payment['method'] == 'payarc') {
                    $paymentService = new PaymentService($payment['method']);
                    
                    // Normalize expiry format to MMYY if it contains a slash
                    $expiry = str_replace('/', '', $payment['expiry']);

                    $response = $paymentService->capture([
                        'amount' => $order->total,
                        'card_number' => $payment['card_number'],
                        'expiry' => $expiry,   // format MMYY as per Payarc spec
                        'cvv' => $payment['cvv'],
                        'firstname' => $shipping['first_name'] ?? '',
                        'lastname' => $shipping['last_name'] ?? '',
                        'email' => $shipping['email'] ?? '',
                        'address' => $shipping['address'] ?? '',
                        'zip' => $shipping['zip'] ?? '',
                    ]);

                    Log::info('Payarc Response Data: ' . json_encode($response));
                }

                if (isset($response) && ($response['response'] === "1" || $response['response_code'] == 100)) {
                    $order->payment_status = 'paid';
                    // Save transaction ID if available
                    $order->payment_transaction_id = $response['transactionid'] ?? null;
                    $order->save();

                    $emailOrderData = $order['customer_name'] = $shipping['first_name'] ?? '-'.' '.$shipping['last_name']?? '';

                    // Send new order notification emails
                    if (sendOrderNotificationAndEmails($emailOrderData)) {
                        Log::info('Payarc Order Emails Sent to Admin & Customer Successfully! Order Number: ' . $order->order_number);
                    } else {
                        Log::warning('Payarc Order Emails Failed to Send! Order Number: ' . $order->order_number);
                    }

                    Log::info('After payment success order updated');

                    // Delete cart and cart items
                    $cart = $this->cartModel->find($cart['id'] ?? null);
                    if ($cart) {
                        $cart->items()->delete();
                        $cart->delete();
                        Log::info('Cart and cart items deleted successfully.');
                    }

                    DB::commit();
                    Log::info('Final Order placed success');

                    return response()->json([
                        'success' => true,
                        'message' => 'You have placed order successfully.',
                        'order_number' => $order->order_number,
                        'amount' => $order->total,
                    ], 200);
                } else {
                    DB::rollback();
                    $errorMessage = $response['responsetext'] ?? 'Unknown error occurred.';
                    Log::error('Payarc Payment Failed: ' . $errorMessage);
                    return response()->json([
                        'success' => false,
                        'message' => 'Payment failed: ' . $errorMessage,
                    ], 500);
                }
            }
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Payment error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function track(Request $request)
    {
        $query = $request->query('query'); // order id or email from ?query=...

        if (!$query) {
            return response()->json(['error' => 'Query parameter is required.'], 400);
        }

        $order = Order::with([ 'items.product'])
            ->where('order_number', $query)
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found.'], 404);
        }

        // Optionally transform data before sending

        return response()->json($order);
    }

    public function orderSuccessInfo(Request $request){
        $order = Order::where('order_number', $request->order_number)->firstOrFail();
        $data = [
            'order_number' => $order->order_number,
            'total' => $order->total,
            'payment_status' => $order->payment_status,
            'order_status' => $order->order_status,
        ];
        
        if ($order) {
            return response()->json([
                'status' => true,
                'message' => 'Order found successfully.',
                'data' =>  $data
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No order data found.',
                'data' => NULL
            ]);
        }
    }
}
