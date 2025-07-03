<div class="invoice-box">
    <div class="print-top-spacing"></div>
    <div class="invoice-print-area">
        <div class="no-print" style="text-align: right; margin-bottom: 20px;">
            <a href="{{ route('download.invoice', $order->id) }}" style="padding: 8px 16px; font-size: 14px; background-color: #036; color: white; border: none; border-radius: 4px; cursor: pointer;">
                Print Invoice
            </a>
        </div>

        <div class="header mt-4">
            <div class="logo" style="width: 50%;">
                @if(isset(settings()->black_logo) && !empty(settings()->black_logo))
                    <img src="{{ asset('storage').'/'.settings()->black_logo }}" style="width: 100%; max-width: 180px;" alt="{{ settings()->name }}" />
                @else
                    <img src="{{ asset('storage/images/default.png') }}" style="width: 100%; max-width: 180px;" alt="Default" />
                @endif
                <div style="margin-top: 10px; font-size: 13px; color: #555; text-decoration: none;">
                    <p style="margin: 4px 0;"><strong>Address:</strong> {{ settings()->address }}</p>
                </div>
            </div>

            <div class="invoice-info" style="width: 50%; text-align: right;">
                <h2 style="margin: 0;">Order Invoice</h2>
                <p style="margin: 5px 0;"><strong>Invoice #:</strong> {{ $order->order_number }}</p>
                <p style="margin: 0;"><strong>Date:</strong> {{ $order->created_at->format('d M Y') }}</p>
            </div>
        </div>

        <div class="info-block">
            <div class="info-box">
                <h3>Shipping To</h3>
                @if(isset($order->shipping))
                    <p>
                        {{ !empty($order->shipping->first_name) ? $order->shipping->first_name : '-' }} {{ $order->shipping->last_name ?? '' }}<br>
                        {{ !empty($order->shipping->address) ? $order->shipping->address. ',' : '-' }} <br />
                        @if(isset($order->shipping->getState) && !empty($order->shipping->getState->name))
                            {{ $order->shipping->getState->name ?? '-' }}, 
                        @endif
                        @if(isset($order->shipping->getCity) && !empty($order->shipping->getCity->name))
                            {{ $order->shipping->getCity->name ?? '' }},
                        @endif
                        
                        {{ !empty($order->shipping->zip) ? $order->shipping->zip. ',' : '' }}
                        <br>
                        {{ !empty($order->shipping->email) ? $order->shipping->email : '-' }}<br>
                        {{ !empty($order->shipping->phone) ? $order->shipping->phone : '-' }}
                    </p>
                @endif
            </div>
            <div class="info-box">
                <h3>Billing Address</h3>
                @if(isset($order->billing))
                    <p>
                        {{ !empty($order->billing->first_name) ? $order->billing->first_name : '-' }} {{ $order->billing->last_name ?? '' }}<br>
                        {{ !empty($order->billing->address) ? $order->billing->address. ',' : '-' }} <br />
                        @if(isset($order->billing->getState) && !empty($order->billing->getState->name))
                            {{ $order->billing->getState->name ?? '-' }}, 
                        @endif
                        @if(isset($order->billing->getCity) && !empty($order->billing->getCity->name))
                            {{ $order->billing->getCity->name ?? '' }},
                        @endif
                        
                        {{ !empty($order->billing->zip) ? $order->billing->zip. ',' : '' }}<br>
                        {{ !empty($order->billing->email) ? $order->billing->email : '-' }}<br>
                        {{ !empty($order->billing->phone) ? $order->billing->phone : '-' }}
                    </p>
                @endif
            </div>
        </div>

        <table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
            <thead>
                <tr>
                    <th style="background-color: #036; color: #ffffff; padding: 8px;">SL</th>
                    <th style="background-color: #036; color: #ffffff; padding: 8px;">Part Number</th>
                    <th style="background-color: #036; color: #ffffff; padding: 8px;">Item Description</th>
                    <th style="background-color: #036; color: #ffffff; padding: 8px; text-align: right;">Qty</th>
                    <th style="background-color: #036; color: #ffffff; padding: 8px; text-align: right;">Unit Price</th>
                    <th style="background-color: #036; color: #ffffff; padding: 8px; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $index => $item)
                    <tr>
                        <td style="padding: 8px;">{{ $index + 1 }}</td>
                        <td style="padding: 8px;">{{ $item->product->mpn ?? '-' }}</td>
                        <td style="padding: 8px;">{{ $item->product->title ?? '-' }}</td>
                        <td style="padding: 8px; text-align: right;">{{ $item->quantity }}</td>
                        <td style="padding: 8px; text-align: right;">{{ currency() }}{{ number_format($item->unit_price, 2) }}</td>
                        <td style="padding: 8px; text-align: right;">{{ currency() }}{{ number_format($item->sub_total, 2) }}</td>
                    </tr>
                @endforeach

                <!-- Optional spacing row -->
                <tr><td style="border: none" colspan="6" style="height: 20px;"></td></tr>

                <!-- Order Summary rows (aligned under Unit Price & Total) -->
                <tr>
                    <td style="border: none" colspan="4"></td>
                    <td style="padding: 8px; text-align: left;"><strong>Sub Total:</strong></td>
                    <td style="padding: 8px; text-align: right;">{{ currency() }}{{ number_format($order->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td style="border: none" colspan="4"></td>
                    <td style="padding: 8px; text-align: left;"><strong>Tax:</strong></td>
                    <td style="padding: 8px; text-align: right;">{{ currency() }}{{ number_format($order->tax, 2) }}</td>
                </tr>
                <tr>
                    <td style="border: none" colspan="4"></td>
                    <td style="padding: 8px; text-align: left;"><strong>Shipping:</strong></td>
                    <td style="padding: 8px; text-align: right;">{{ currency() }}{{ number_format($order->shipping_cost, 2) }}</td>
                </tr>
                <tr>
                    <td style="border: none" colspan="4"></td>
                    <td style="padding: 8px; text-align: left;"><strong>Promotion Discount:</strong></td>
                    <td style="padding: 8px; text-align: right;">-{{ currency() }}0.00</td>
                </tr>
                <tr>
                    <td style="border: none" colspan="4"></td>
                    <td style="padding: 8px; text-align: left;"><strong>Total:</strong></td>
                    <td style="padding: 8px; text-align: right;"><strong>{{ currency() }}{{ number_format($order->total, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>

        <div style="clear: both;"></div>

        <p><strong>Payment Method:</strong> {{ ucfirst($order->payment_method) }}<br>
        <div><strong>Payment Status:</strong> {{ ucfirst($order->payment_status) }}<br>

        <div class="footer">
            <p>Need help? Email: {{ settings()->support_email }} | Phone: {{ settings()->phone_number }}</p>
            <p>Website: <a href="{{ settings()->website_url }}">www.soliddiskdirect.com</a></p>
            <p>© {{ now()->year }} {{ appName() }}. All rights reserved.</p>
        </div>
    </div>
</div>
<style>
    body {
        font-family: 'Arial', sans-serif;
        font-size: 14px;
        color: #333;
        margin: 0;
        padding: 20px;
        background: #f7f7f7;
    }

    .invoice-box {
        max-width: 900px;
        margin: auto;
        padding: 30px;
        background: #fff;
        border: 1px solid #eee;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        border-bottom: 1px solid #ddd;
        padding-bottom: 20px;
        margin-bottom: 30px;
    }

    .logo {
        max-width: 150px;
    }

    .invoice-info {
        text-align: right;
    }

    .invoice-info h2 {
        margin: 0;
        font-size: 20px;
        color: #444;
    }

    .info-block {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
    }

    .info-box {
        width: 48%;
        line-height: 1.5;
    }

    h3 {
        border-bottom: 1px solid #eee;
        padding-bottom: 5px;
        margin-bottom: 10px;
        font-size: 16px;
        color: #444;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
    }

    table th, table td {
        padding: 10px;
        border: 1px solid #eee;
        text-align: left;
    }

    table th {
        background: #f0f0f0;
        font-weight: bold;
    }

    .total-table {
        width: 50%;
        float: right;
        margin-top: 20px;
    }

    .total-table td {
        padding: 10px;
        border: none;
    }

    .total-table tr.total-row td {
        font-weight: bold;
        border-top: 2px solid #333;
    }

    .footer {
        font-size: 13px;
        text-align: center;
        color: #777;
        border-top: 1px solid #eee;
        padding-top: 20px;
    }
    @media print {
        body * {
            visibility: hidden;
        }

        .invoice-print-area, .invoice-print-area * {
            visibility: visible;
        }

        .invoice-print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 0;
            margin: 0;
        }

        html, body {
            height: auto !important;
            overflow: hidden;
        }

        .invoice-box {
            box-shadow: none;
            border: none;
        }

        .no-print {
            display: none !important;
        }
    }
</style>

{{-- <h5>Order #{{ $order->order_number }}</h5>
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

<div style="display: flex; justify-content: flex-end; margin-top: 10px;">
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
 --}}
