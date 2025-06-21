<?php

namespace App\Http\Controllers\Api;

use Exception;
use Stripe\Stripe;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Services\PayarcService;
use App\Mail\OrderConfirmedAdmin;
use Illuminate\Support\Facades\DB;
use App\Models\OrderBillingAddress;
use App\Models\OrderShippingMethod;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Mail\OrderConfirmedCustomer;
use App\Models\OrderShippingAddress;
use Illuminate\Support\Facades\Mail;
use App\Services\FedExShippingService;
use App\Http\Requests\PlaceOrderRequest;
use App\Services\Payment\PaymentService;
use Illuminate\Support\Facades\Validator;
use App\Notifications\SiteEventNotification;
use App\Models\{BuyNow, Cart, ShippingMethod, Order};

class OrderController extends Controller
{
    protected $paymentService;
    protected $orderModel;
    protected $buyNowModel;
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
        $this->buyNowModel = new BuyNow();
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
        $buyNowProduct = $request->buyNowProduct;
        if(!isset($buyNowProduct) && empty($buyNowProduct)){
            $cart = $request->cart;
            $cartItems = $cart['items'];
        }
        
        DB::beginTransaction();
        try {
            $orderNumber = null;
            do {
                $orderNumber = 'ORD' . strtoupper(substr(bin2hex(random_bytes(2)), 0, 5));
            } while (Order::where('order_number', $orderNumber)->exists());

            $order = $this->orderModel;
            $order->order_number = $orderNumber;
            $order->customer_id = auth()->check() ? auth()->id() : null;

            if(!isset($buyNowProduct) && empty($buyNowProduct)){
                $order->session_id = auth()->check() ? null : $cart['session_id'];
                $order->coupon_id = NULL;
                $order->same_as_shipping = $sameAsShipping;
                $order->subtotal = $cart['subtotal'] ?? 0;
                $order->tax = $cart['tax_amount'] ?? 0;
                $order->shipping_weight = $cart['shipping_weight'] ?? 0;
                $order->shipping_cost = $cart['shipping_cost'] ?? 0;
                $order->total = $cart['total'] ?? 0;
                $order->payment_method = $payment['method'];
                $order->payment_status = 'unpaid';
                $order->additional_note = null;
                $order->save();

                Log::info('Order Added Successfully: '.json_encode($order));

                if($order && $cartItems){
                    foreach($cartItems as $item){
                        $product = $this->productModel->where('slug', $item['product']['slug'])->first();
                        $shippingWeight = getWeightOnlyAttribute($product->shipping_weight);
                        if(!empty($product)){
                            $order_item = $this->orderItemModel;
                            $order_item->order_id = $order->id;
                            $order_item->product_id = $product->id;
                            $order_item->variant_id = null;
                            $order_item->shipping_weight = $shippingWeight*$item['quantity'] ?? 0;
                            $order_item->unit_price = $item['unit_price'] ?? 0;
                            $order_item->discount = null;
                            $order_item->quantity = $item['quantity'] ?? 0;
                            $order_item->options = null;
                            $order_item->sub_total = $item['sub_total'] ?? 0;
                            $order_item->save();
                        }
                    }

                    Log::info('Order Item Added Successfully: '.json_encode($order_item));
                }
            }else{
                $order->session_id = auth()->check() ? null : $buyNowProduct['session_id'];
                $order->coupon_id = NULL;
                $order->same_as_shipping = $sameAsShipping;
                $order->subtotal = $buyNowProduct['total'] ?? 0;
                $order->tax = $buyNowProduct['tax_amount'] ?? 0;
                $order->shipping_weight = $buyNowProduct['shipping_weight'] ?? 0;
                $order->shipping_cost = $buyNowProduct['shipping_cost'] ?? 0;
                $order->total = $buyNowProduct['total'] ?? 0;

                $order->payment_method = $payment['method'];
                $order->payment_status = 'unpaid';
                $order->additional_note = null;
                $order->save();

                Log::info('Order Added Successfully: '.json_encode($order));

                if($order && $buyNowProduct['product']){
                    $product = $this->productModel->where('slug', $buyNowProduct['product']['slug'])->first();
                    if(!empty($product)){
                        $shippingWeight = getWeightOnlyAttribute($product->shipping_weight);

                        $order_item = $this->orderItemModel;
                        $order_item->order_id = $order->id;
                        $order_item->product_id = $product->id;
                        $order_item->variant_id = null;
                        $order_item->shipping_weight = $shippingWeight*$buyNowProduct['quantity'] ?? 0;
                        $order_item->unit_price = $buyNowProduct['unit_price'] ?? 0;
                        $order_item->discount = null;
                        $order_item->quantity = $buyNowProduct['quantity'] ?? 0;
                        $order_item->options = null;
                        $order_item->sub_total = $buyNowProduct['unit_price'] ?? 0;
                        $order_item->save();
                    }

                    Log::info('Order Item Added Successfully: '.json_encode($order_item));
                }
            }

            if(isset($shipping) && !empty($shipping)){
                $order_shipping_address = $this->orderShippingAddress;
                $order_shipping_address->order_id = $order->id;
                $order_shipping_address->first_name = $shipping['first_name'] ?? '';
                $order_shipping_address->last_name = $shipping['last_name'] ?? '';
                $order_shipping_address->email = $shipping['email'] ?? '';
                $order_shipping_address->phone = $shipping['phone'] ?? '';
                $order_shipping_address->address = $shipping['address'] ?? '';
                $order_shipping_address->address_line_2 = $shipping['address_line_2'] ?? '';
                $order_shipping_address->city = $shipping['shippingCity'] ?? '';
                $order_shipping_address->state = $shipping['shippingState'] ?? '';
                $order_shipping_address->zip = $shipping['zip'] ?? '';
                $order_shipping_address->country = $shipping['shippingCountry'] ?? '';
                $order_shipping_address->save();

                Log::info('Order Shipping Address Added Successfully');
            }

            if(isset($sameAsShipping) && $sameAsShipping==false && !empty($billing)){
                $order_billing_address = $this->orderBillingAddress;  
                $order_billing_address->order_id = $order->id;
                $order_billing_address->first_name = $billing['first_name'] ?? '';
                $order_billing_address->last_name = $billing['last_name'] ?? '';
                $order_billing_address->email = $billing['email'] ?? '';
                $order_billing_address->phone = $billing['phone'] ?? '';
                $order_billing_address->address = $billing['address'] ?? '';
                $order_billing_address->address_line_2 = $billing['address_line_2'] ?? '';
                $order_billing_address->country = $billing['billCountry'] ?? '';
                $order_billing_address->state = $billing['billState'] ?? '';
                $order_billing_address->city = $billing['billCity'] ?? '';
                $order_billing_address->zip = $billing['zip'] ?? '';
                $order_billing_address->save();

                Log::info('Order Billing Address Added Successfully');
            }

            if(!isset($buyNowProduct) && empty($buyNowProduct)){
                $order_shipping_service = $this->orderShippingService->where('cart_id', $cart['id'])->first();
                if(isset($order_shipping_service) && !empty($order_shipping_service)){
                    $order_shipping_service->order_id = $order->id;
                    $order_shipping_service->save();

                    Log::info('Order Shipping Service updated Successfully');
                }
            }

            //paypal 
            if ($payment['method'] == 'paypal') {
                DB::commit();
                $paymentService = new PaymentService('paypal');
                $paypalResponse = $paymentService->createPaypalOrder($order); 

                // Save PayPal order ID
                $order->paypal_order_id = $paypalResponse['id'];
                $order->save();

                if(!isset($buyNowProduct) && empty($buyNowProduct)){
                    $cart = $this->cartModel->find($cart['id']);
                    if ($cart) {
                        $cart->items()->delete();
                        $cart->delete();

                        Log::info('Cart and cart item deleted successfully. ');
                    }
                }else{
                    $this->buyNowModel->where('session_id', $buyNowProduct['session_id'])->delete();
                    Log::info('Buy now deleted successfully. ');
                }

                $approveLink = collect($paypalResponse['links'])->firstWhere('rel', 'approve')['href'];
                return response()->json([
                    'redirect_url' => $approveLink,
                    'success' => true,
                    'message' => 'Redirecting to PayPal for payment',
                ]);
            }elseif ($payment['method'] == 'payarc') {
                $payarc = new PayarcService();
                $month = '';
                $year = '';
                $expiry = trim($payment['expiry']); // Remove extra whitespace
                if (preg_match('/^(0[1-9]|1[0-2])\/([0-9]{2})$/', $expiry, $matches)) {
                    $month = $matches[1]; // "06"
                    $year = '20' . $matches[2]; // "2026"
                } else {
                    throw new Exception("Invalid expiry date format: $expiry");
                }

                $totalAmount = '';
                if(!isset($buyNowProduct) && empty($buyNowProduct)){
                    $totalAmount = (int) ($cart['total'] * 100);
                }else{
                    $totalAmount = (int) ($buyNowProduct['total'] * 100);
                }

                $response = $payarc->createPaymentIntent([
                    'card_number' => $payment['card_number'],
                    'exp_month'   => $month,
                    'exp_year'    => $year,
                    'cvv'         => $payment['cvv'],
                    'card_holder' => $payment['name'] ?? '', // fallback
                    'amount'      => $totalAmount, // cents
                    'currency'    => 'usd', 
                ]);

                $data = $response['data'] ?? [];
                
                if (isset($data['host_response_message'], $data['status']) && $data['status'] === 'submitted_for_settlement' && $data['host_response_message'] === 'Success' ) {
                    $transactionId = $data['id'] ?? null;
                    $order->payment_status = 'paid';
                    $order->transaction_id = $transactionId ?? null;
                    $order->save();

                    // Send new order notification emails
                    if (sendOrderNotificationAndEmails($order)) {
                        Log::info('Payarc Order Emails Sent to Admin & Customer Successfully! Order Number: ' . $order->order_number);
                    } else {
                        Log::warning('Payarc Order Emails Failed to Send! Order Number: ' . $order->order_number);
                    }

                    // Delete cart and cart items
                    if(!isset($buyNowProduct) && empty($buyNowProduct)){
                        $cart = $this->cartModel->find($cart['id'] ?? null);
                        if ($cart) {
                            $cart->items()->delete();
                            $cart->delete();
                            Log::info('Cart and cart items deleted successfully.');
                        }
                    }else{
                        $this->buyNowModel->where('session_id', $buyNowProduct['session_id'])->delete();
                        Log::info('Buy now deleted successfully. ');
                    }

                    DB::commit();
                    Log::info('Final Order placed success');

                    return response()->json([
                        'success' => true,
                        'message' => 'You have placed order successfully.',
                        'order_number' => $order->order_number,
                        'amount' => $order->total,
                        'payment_method' => 'payarc',
                    ], 200);
                } else {
                    DB::rollback();
                    $errorMessage = 'Credentials not match';
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
        $request->validate([
            'query' => 'required|string',
            'email' => 'required|email',
        ]);

        $query = $request->query('query'); // order id or email from ?query=...

        if (!$query) {
            return response()->json(['error' => 'Query parameter is required.'], 400);
        }

        $order = Order::where('order_number', $query)->first();
        if (!$order) {
            return response()->json(['error' => 'Order not found.'], 404);
        }else if(!empty($order)){
            $orderShipping = $this->orderShippingAddress->where('order_id', $order->id)->where('email', $request->email)->first();

            if(!empty($orderShipping)){
                return response()->json($order);
            }else{
                return response()->json(['error' => 'Shipping email not matched.'], 404);
            }
        }
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
