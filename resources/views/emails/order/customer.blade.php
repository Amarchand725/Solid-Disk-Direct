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

# Order Confirmation - #{{ $order->order_number }}

@php $customer = null @endphp
@if(isset($order->shipping) && !empty($order->shipping))
    @php $customer = $order->shipping @endphp
@endif

Hi {{ $customer->first_name ?? '' }} {{ $customer->last_name ?? '' }}   ,

Thank you for your order! Here are your order details:

**Order ID:** {{ $order->order_number }}  
**Order Date:** {{ $order->created_at->format('d M Y') }}  
**Tax Amount:** ${{ number_format($order->tax, 2) }}  
**Shipping Amount:** ${{ number_format($order->shipping_cost, 2) }}  
**Total Amount:** ${{ number_format($order->total, 2) }}

@component('mail::table')
| Product       | Qty | Price   | SubTotal  |
| ------------- |:---:| -------:|----------:|
@foreach($order->items as $item)
| {{ $item->product->mpn }} {{ $item->product->title }} | {{ $item->quantity }} | ${{ number_format($item->unit_price, 2) }} |${{ number_format($item->sub_total, 2) }} |
@endforeach
@endcomponent

@component('mail::button', ['url' => env('FRONTEND_BASE_URL').'/track-order'])
View Your Order
@endcomponent

If you have any questions, feel free to contact us.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
