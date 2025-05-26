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
        // $this->paymentService = $paymentService;
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
        // $payment = $request->payment;
        // return $payment['method'];
        $validated = $requestValidated->validated();
        $payment = $request->payment;
        $shipping = $validated['shipping'];
        $billing = $validated['billing'];
        $sameAsShipping = $billing['same_as_shipping'];
        $cart = $request->cart;
        $cartItems = $cart['items'];
        $order_shipping_service = $this->orderShippingService->where('cart_id', $cart['id'])->first();
        $weight = 0;
        
        DB::beginTransaction();
        try {
            $order = $this->orderModel;
            $order->order_number = 'ORD' . strtoupper(substr(bin2hex(random_bytes(2)), 0, 5));
            $order->customer_id = auth()->check() ? auth()->id() : null;
            $order->session_id = auth()->check() ? null : $cart['session_id'];
            $order->coupon_id = NULL;
            $order->same_as_shipping = $sameAsShipping;
            $order->subtotal = $cart['subtotal'] ?? 0;
            $order->shipping_cost = $cart['shipping_cost'] ?? 0;
            // $order->tax = null;
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
                        $weight += $product->product_weight;
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

                    // Log::info('Order Shipping Address Added Successfully');
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

                // $fedExService = new FedExShippingService();

                
                // $payload = [
                //     "accountNumber" => [
                //         "value" => env('FEDEX_ACCOUNT_NUMBER')
                //     ],
                //     "labelResponseOptions" => "URL_ONLY", // required
                //     "requestedShipment" => [
                //         "shipper" => [
                //             "contact" => [
                //                 "personName" => "John Doe",
                //                 "phoneNumber" => "1234567890"
                //             ],
                //             "address" => [
                //                 "streetLines" => ["123 Main St"],
                //                 "city" => "New York",
                //                 "stateOrProvinceCode" => "NY",
                //                 "postalCode" => "10001",
                //                 "countryCode" => "US"
                //             ]
                //         ],
                //         "recipients" => [
                //             [
                //                 "contact" => [
                //                     "personName" => $shipping['first_name'],
                //                     "phoneNumber" => $shipping['phone']
                //                 ],
                //                 "address" => [
                //                     "streetLines" => [$shipping['address']],
                //                     "city" => $shipping['shippingCity'],
                //                     "stateOrProvinceCode" => 'CA',
                //                     "postalCode" => '94105',
                //                     "countryCode" => 'Us'
                //                 ]
                //             ]
                //         ],
                //         "pickupType" => "DROPOFF_AT_FEDEX_LOCATION",
                //         "serviceType" => "FEDEX_GROUND",
                //         "packagingType" => "YOUR_PACKAGING",
                //         "shippingChargesPayment" => [
                //             "paymentType" => "SENDER",
                //             "payor" => [
                //                 "responsibleParty" => [
                //                     "accountNumber" => [
                //                         "value" => env('FEDEX_ACCOUNT_NUMBER')
                //                     ]
                //                 ]
                //             ]
                //         ],
                //         "labelSpecification" => [
                //             "labelFormatType" => "COMMON2D",
                //             "imageType" => "PDF",
                //             "labelStockType" => "PAPER_4X6"
                //         ],
                //         "requestedPackageLineItems" => [
                //             [
                //                 "weight" => [
                //                     "units" => "KG",
                //                     "value" => $weight
                //                 ]
                //             ]
                //         ]
                //     ]
                // ];

                // $fedexResponse = $fedExService->createShipment($payload);


                //paypal 
                if ($payment['method'] == 'paypal') {
                    DB::commit();
                    $paymentService = new PaymentService('paypal');
                    $paypalResponse = $paymentService->createPaypalOrder($order); 

                    // Save PayPal order ID
                    $order->paypal_order_id = $paypalResponse['id'];

                    // $order->tracking_number = $fedexResponse['output']['transactionShipments'][0]['masterTrackingNumber'];
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
                }elseif($payment['method']=='payarc'){
                    $paymentService = new PaymentService($payment['method']);
                    $response = $paymentService->capture([
                        'amount' => $order->total,
                        'card_number' => $payment['card_number'],
                        'expiry' => $payment['expiry'],   // format MMYY or MM/YY as per Payarc spec
                        'cvv' => $payment['cvv'],
                        'firstname' => $shipping['first_name'] ?? '',
                        'lastname' => $shipping['last_name'] ?? '',
                        'email' => $shipping['email'] ?? '',
                        'address' => $shipping['address'] ?? '',
                        'zip' => $shipping['zip'] ?? '',
                    ]);
                }
                // else{ //if use stripe
                //     $paymentIntent = $this->paymentService->handleStripePayment($order->total, $request->payment_method_id);
                //     Log::info('Payment Response: '.json_encode($paymentIntent));
                // }

                //Strip
                // $paymentIntent = $this->paymentService->handleStripePayment($order->total, $request->payment_method_id);
                // Log::info('Payment Response: '.json_encode($paymentIntent));
                // if($paymentIntent->status=='succeeded'){
                //Strip
                if($response){
                    $order->payment_status = 'paid';
                    // $order->transaction_id = $paymentIntent->id;
                    $order->save();

                    //new order notification
                    $admin = getActiveAdminUser();
                    if(!empty($admin)){
                        $customerName = $shipping['first_name'].' '.$shipping['last_name'];
                        $url = route('orders.index');
                        $admin->notify(new SiteEventNotification('subscribe.png', 'New Order Placed', "{$customerName} has placed order.", $url));

                        //order confirm email
                        Mail::to('orders@soliddiskdirect.com')->queue(new OrderConfirmedAdmin($order));
                    }

                    //order confirm customer email
                    Mail::to($shipping['email'])->queue(new OrderConfirmedCustomer($order));
                    //order confirm customer emails

                    Log::info('After payment success order updated');

                    // Step 1: Delete all cart items associated with the cart
                    $cart = $this->cartModel->find($cart['id']);
                    if ($cart) {
                        $cart->items()->delete();
                        $cart->delete();

                        Log::info('Cart and cart item deleted successfully. ');
                    }
                    // Step 2: Delete the cart itself

                    DB::commit();
                    Log::info('Final Order placed success ');
                    return response()->json([   
                        'success' => true, 
                        'message' =>'You have placed order successfully.',
                        'order_number' => $order->order_number,
                    ], 200);
                }
            }
        } catch (Exception $e) {
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
            ->where('id', $query)
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found.'], 404);
        }

        // Optionally transform data before sending

        return response()->json($order);
    }
}
