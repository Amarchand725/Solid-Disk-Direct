@component('mail::message')
<table width="100%" style="text-align: center; margin-bottom: 20px;">
    <tr>
        <td>
            @if(isset(settings()->black_logo) && !empty(settings()->black_logo))
                <img src="{{ asset('storage').'/'.settings()->black_logo }}" style="height: 40px;" alt="{{ settings()->name }}" />
            @else
                <img src="{{ asset('storage/images/default.png') }}" style="height: 40px;" alt="Default" />
            @endif
        </td>
    </tr>
</table>

# New Order Received

@php $customer = null @endphp
@if(isset($order->shipping) && !empty($order->shipping))
    @php $customer = $order->shipping @endphp
@endif

**Order ID:** #{{ $order->order_number }}  
**Customer:** {{ $customer->first_name ?? '' }} {{ $customer->last_name ?? '' }}  
**Email:** {{ $customer->email ?? '' }}  
**Total:** ${{ number_format($order->total, 2) }}

@component('mail::button', ['url' => url('/admin/orders')])
View Order in Admin Panel
@endcomponent

@endcomponent
