@component('mail::message')
# Order Confirmation - #{{ $order->id }}

Hi {{ $order->customer_name }},

Thank you for your order! Here are your order details:

**Order ID:** {{ $order->id }}  
**Order Date:** {{ $order->created_at->format('d M Y') }}  
**Total Amount:** ${{ number_format($order->total, 2) }}

@component('mail::table')
| Product       | Qty | Price   |
| ------------- |:---:| -------:|
@foreach($order->items as $item)
| {{ $item->product_name }} | {{ $item->quantity }} | ${{ number_format($item->price, 2) }} |
@endforeach
@endcomponent

@component('mail::button', ['url' => url('/orders/'.$order->order_number)])
View Your Order
@endcomponent

If you have any questions, feel free to contact us.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
