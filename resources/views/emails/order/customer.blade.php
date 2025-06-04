@component('mail::message')
# Order Confirmation - #{{ $order->order_number }}

Hi {{ $order['customer_name'] ?? '' }},

Thank you for your order! Here are your order details:

**Order ID:** {{ $order->order_number }}  
**Order Date:** {{ $order->created_at->format('d M Y') }}  
**Total Amount:** ${{ number_format($order->total, 2) }}

@component('mail::table')
| Product       | Qty | Price   |
| ------------- |:---:| -------:|
@foreach($order->items as $item)
| {{ $item->product->title }} | {{ $item->quantity }} | ${{ number_format($item->price, 2) }} |
@endforeach
@endcomponent

@component('mail::button', ['url' => env('FRONTEND_BASE_URL').'/track-order'])
View Your Order
@endcomponent

If you have any questions, feel free to contact us.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
