<h5>Order #{{ $order->order_number }}</h5>
<p style="margin: 0;"><strong>Status:</strong> 
    @php
        $statusClass = orderStatus()[$order->order_status] ?? 'secondary';
    @endphp
    <span class="badge bg-{{ $statusClass }}">
        {{ ucfirst($order->order_status) }}
    </span>
</p>

<p style="margin: 0;"><strong>Total:</strong> {{ currency() }}{{ number_format($order->total, 2) }}</p>
<p style="margin: 0;"><strong>Date:</strong> {{ $order->created_at->format('d M Y') }}</p>
<p><strong>Payment Method:</strong> <span class="badge bg-info">{{ ucfirst($order->payment_method) }}</span></p>
<p><strong>Payment Status:</strong> <span @if($order->payment_status=='paid') class="badge bg-success" @else class="badge bg-danger" @endif>{{ ucfirst($order->payment_status) }}</span></p>

<hr>
<h6>Customer Info</h6>
@if(isset($order->customer) && !empty($order->customer))
    <p>{{ $order->customer->first_name ?? '' }} {{ $order->customer->last_name ?? '' }} ({{ $order->customer->email }})</p>
@else
    <p>Not Available</p>
@endif

<hr>
<div style="display: flex; gap: 20px; justify-content: space-between;">
    
    <!-- Shipping Address -->
    <div style="flex: 1;">
        <h6>Shipping Address</h6>
        @if(isset($order->shipping) && !empty($order->shipping))
            <p>
                {{ $order->shipping->first_name ?? '-' }} {{ $order->shipping->last_name ?? '' }}<br>
                {{ $order->shipping->address ?? '-' }}<br>
                @if(isset($order->shipping->getState))
                    {{ $order->shipping->getState->name ?? '-' }},
                @endif
                @if(isset($order->shipping->getCity))
                    {{ $order->shipping->getCity->name ?? '-' }},
                @endif
                {{ $order->shipping->zip ?? '-' }}<br>
                {{ $order->shipping->email ?? '-' }}<br>
                {{ $order->shipping->phone ?? '-' }}
            </p>
        @else
            <p>Not Found Shipping Address</p>
        @endif
    </div>

    <!-- Billing Address -->
    <div style="flex: 1;">
        <h6>Billing Address</h6>
        @if(isset($order->billing) && !empty($order->billing))
            <p>
                {{ $order->billing->first_name ?? '-' }} {{ $order->billing->last_name ?? '' }}<br>
                {{ $order->billing->address ?? '-' }}<br>
                @if(isset($order->billing->getState))
                    {{ $order->billing->getState->name ?? '-' }},
                @endif
                @if(isset($order->billing->getCity))
                    {{ $order->billing->getCity->name ?? '-' }},
                @endif
                {{ $order->billing->zip ?? '-' }}<br>
                {{ $order->billing->email ?? '-' }}<br>
                {{ $order->billing->phone ?? '-' }}
            </p>
        @else
            <p>Not Found Billing Address</p>
        @endif
    </div>
</div>

<hr>
<h6>Order Items</h6>
<table class="table">
    <thead>
        <tr>
            <th>Part Number</th>
            <th>Product</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($order->items as $item)
        <tr>
            <td>{{ $item->product->mpn ?? '-' }}</td>
            <td>{{ $item->product->title ?? '-' }}</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ currency() }}{{ number_format($item->unit_price, 2) }}</td>
            <td>{{ currency() }}{{ number_format($item->sub_total, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div style="display: flex; justify-content: flex-end; margin-top: -10px;">
    <table style="width: 300px; border-collapse: collapse;">
        <tr>
            <td style="padding: 8px; border: 1px solid #eee;">Sub Total:</td>
            <td style="padding: 8px; border: 1px solid #eee;">
                {{ currency() }}{{ number_format($order->subtotal, 2) }}
            </td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #eee;">Tax:</td>
            <td style="padding: 8px; border: 1px solid #eee;">
                {{ currency() }}{{ number_format($order->tax, 2) }}
            </td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #eee;">Shipping:</td>
            <td style="padding: 8px; border: 1px solid #eee;">
                {{ currency() }}{{ number_format($order->shipping_cost, 2) }}
            </td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #eee;">Promotion Discount:</td>
            <td style="padding: 8px; border: 1px solid #eee;">
                -{{ currency() }}0.00
            </td>
        </tr>
        <tr style="font-weight: bold;">
            <td style="padding: 10px; border: 1px solid #eee;">Total:</td>
            <td style="padding: 10px; border: 1px solid #eee;">
                {{ currency() }}{{ number_format($order->total, 2) }}
            </td>
        </tr>
    </table>
</div>

<div style="clear: both;"></div>

