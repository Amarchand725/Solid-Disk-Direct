@component('mail::message')
# New Order Received

**Order ID:** #{{ $order->order_number }}  
**Customer:** {{ $order->customer_name }}  
**Email:** {{ $order->customer_email }}  
**Total:** ${{ number_format($order->total, 2) }}

@component('mail::button', ['url' => url('/admin/orders/'.$order->id)])
View Order in Admin Panel
@endcomponent

@endcomponent
