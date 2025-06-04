@component('mail::message')
# New Order Received

@php $customer = null @endphp
@if(isset($order->shipping) && !empty($order->shipping))
    @php $customer = $order->shipping @endphp
@endif

**Order ID:** #{{ $order->order_number }}  
**Customer:** {{ $customer->first_name ?? '' }} {{ $customer->last_name ?? '' }}  
**Email:** {{ $customer->email ?? '' }}  
**Total:** ${{ number_format($order->total, 2) }}

@component('mail::button', ['url' => url('/admin/orders/'.$order->id)])
View Order in Admin Panel
@endcomponent

@endcomponent
