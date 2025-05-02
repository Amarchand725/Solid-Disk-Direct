<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use App\Services\PaymentService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\PlaceOrderRequest;
use App\Models\{Cart, ShippingMethod, Order};

class OrderController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function store(PlaceOrderRequest $request)
    {
        $customer = auth()->user();
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $cart = Cart::with('items')->where('customer_id', $customer->id)->firstOrFail();

            if ($cart->items->isEmpty()) {
                return response()->json(['error' => 'Cart is empty'], 400);
            }

            $subtotal = $cart->items->sum(fn($item) => $item->price * $item->quantity);
            $shippingMethod = ShippingMethod::findOrFail($data['shipping_method_id']);
            $shippingCost = $shippingMethod->cost;
            $total = $subtotal + $shippingCost;

            if ($data['payment']['method'] === 'stripe') {
                $this->paymentService->handleStripePayment(
                    $total,
                    $data['payment']['stripe_token'],
                    $customer->email
                );
            }

            $order = Order::create([
                'customer_id' => $customer->id,
                'order_number' => 'ORD' . strtoupper(uniqid()),
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'shipping_method_id' => $shippingMethod->id,
                'payment_method' => $data['payment']['method'],
                'payment_status' => 'paid',
                'status' => 'processing'
            ]);

            $order->shipping()->create($data['shipping']);

            $billing = $data['billing']['same_as_shipping']
                ? $data['shipping']
                : '';

            if(!empty($billing)){
                $order->billing()->create($billing);
            }else{
                $order->same_as_shipping = 1;
            }

            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'discount' => $item->discount,
                    'options' => $item->options,
                    'sub_total' => $item->sub_total,
                ]);
            }

            $cart->items()->delete();
            $cart->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'order_id' => $order->order_number,
                "redirect_url" => "/thank-you?order_id=".$order->order_number
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Order failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
