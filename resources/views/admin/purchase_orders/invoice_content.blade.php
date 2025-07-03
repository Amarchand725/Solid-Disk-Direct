<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order Invoice #{{ $order->po_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 14px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .invoice-box {
            max-width: 1000px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            background: #fff;
        }
        .header {
            width: 100%;
            display: table;
            margin-bottom: 20px;
        }
        .logo, .invoice-info {
            display: table-cell;
            vertical-align: top;
        }
        .logo {
            width: 50%;
        }
        .invoice-info {
            width: 50%;
            text-align: right;
        }
        .info-block {
            display: table;
            width: 100%;
            margin-top: 20px;
        }
        .info-box {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            font-size: 12px;
        }
        table, th, td {
            border: 1px solid #eee;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        .footer {
            margin-top: 40px;
            font-size: 11px;
            text-align: center;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <div class="logo">
                @php
                    $logoPath = settings()->black_logo 
                        ? public_path('storage/' . settings()->black_logo) 
                        : public_path('storage/images/default.png');
                @endphp
                <img src="{{ $logoPath }}" style="width: 100%; max-width: 180px;" alt="Logo">
                <div style="margin-top: 10px; font-size: 13px; color: #555;">
                    <p><strong>Billing Address:</strong><br /> {{ settings()->address }}</p>
                </div>
            </div>

            <div class="invoice-info">
                <h2 style="margin: 0 0 5px 0;">PURCHASE ORDER</h2>
                <p style="margin: 0;"><strong>PO. NUMBER #:</strong> {{ $order->po_number }}</p>
                <p style="margin: 0;"><strong>REF. ID #:</strong> {{ $order->order_number }}</p>
                <p style="margin: 0;"><strong>Date:</strong> {{ $order->created_at->format('d M Y') }}</p>
            </div>
        </div>

        <div class="info-block">
            <div class="info-box">
                <h3>VENDOR DETAILS:</h3>
                @if(isset($order->getVendor))
                    <p>
                        {{ !empty($order->getVendor->first_name) ? $order->getVendor->first_name : '-' }} {{ $order->getVendor->last_name ?? '' }}<br>
                        {{ !empty($order->getVendor->address) ? $order->getVendor->address. ',' : '-' }} <br />
                        @if(isset($order->getVendor->getState) && !empty($order->getVendor->getState->name))
                            {{ $order->getVendor->getState->name ?? '-' }}, 
                        @endif
                        @if(isset($order->getVendor->getCity) && !empty($order->getVendor->getCity->name))
                            {{ $order->getVendor->getCity->name ?? '' }},
                        @endif
                        
                        {{ !empty($order->getVendor->zip) ? $order->getVendor->zip. ',' : '' }}<br>
                        {{ !empty($order->getVendor->email) ? $order->getVendor->email : '-' }}<br>
                        {{ !empty($order->getVendor->phone) ? $order->getVendor->phone : '-' }}
                    </p>
                @endif
            </div>

            <div class="info-box">
                <h3>SHIPPING ADDRESS:</h3>
                @if(isset($order->getOrder->shipping))
                    <p>
                        {{ !empty($order->getOrder->shipping->first_name) ? $order->getOrder->shipping->first_name : '-' }} {{ $order->getOrder->shipping->last_name ?? '' }}<br>
                        {{ !empty($order->getOrder->shipping->address) ? $order->getOrder->shipping->address. ',' : '-' }} <br />
                        @if(isset($order->getOrder->shipping->getState) && !empty($order->getOrder->shipping->getState->name))
                            {{ $order->getOrder->shipping->getState->name ?? '-' }}, 
                        @endif
                        @if(isset($order->getOrder->shipping->getCity) && !empty($order->getOrder->shipping->getCity->name))
                            {{ $order->getOrder->shipping->getCity->name ?? '' }},
                        @endif
                        
                        {{ !empty($order->getOrder->shipping->zip) ? $order->getOrder->shipping->zip. ',' : '' }}
                        <br>
                        {{ !empty($order->getOrder->shipping->email) ? $order->getOrder->shipping->email : '-' }}<br>
                        {{ !empty($order->getOrder->shipping->phone) ? $order->getOrder->shipping->phone : '-' }}
                    </p>
                @endif
            </div>
        </div>
        

        <table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
            <thead>
                <tr>
                    <th style="background-color: #036; color: #ffffff; padding: 8px;">SL</th>
                    <th style="background-color: #036; color: #ffffff; padding: 8px;">Part Number</th>
                    <th style="background-color: #036; color: #ffffff; padding: 8px;">Description</th>
                    <th style="background-color: #036; color: #ffffff; padding: 8px;">Condition</th>
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
                        <td style="padding: 8px;">{!! $item->product->short_description ?? '-' !!}</td>
                        <td style="padding: 8px;">{{ $item->product->condition ?? '-' }}</td>
                        <td style="padding: 8px; text-align: right;">{{ $item->quantity }}</td>
                        <td style="padding: 8px; text-align: right;">{{ currency() }}{{ number_format($item->unit_price, 2) }}</td>
                        <td style="padding: 8px; text-align: right;">{{ currency() }}{{ number_format($item->sub_total, 2) }}</td>
                    </tr>
                @endforeach

                <tr>
                    <td style="border: 0; background: none; padding: 0;" colspan="5"></td>
                    <td style="padding: 8px; text-align: left;"><strong>Sub Total:</strong></td>
                    <td style="padding: 8px; text-align: right;">{{ currency() }}{{ number_format($order->sub_total, 2) }}</td>
                </tr>
                <tr>
                    <td style="border: 0; background: none; padding: 0;" colspan="5"></td>
                    <td style="padding: 8px; text-align: left;"><strong>Tax:</strong></td>
                    <td style="padding: 8px; text-align: right;">{{ currency() }}{{ number_format($order->tax, 2) }}</td>
                </tr>
                <tr>
                    <td style="border: 0; background: none; padding: 0;" colspan="5"></td>
                    <td style="padding: 8px; text-align: left;"><strong>Shipping:</strong></td>
                    <td style="padding: 8px; text-align: right;">{{ currency() }}{{ number_format($order->shipping_charges, 2) }}</td>
                </tr>
                <tr>
                    <td style="border: 0; background: none; padding: 0;" colspan="5"></td>
                    <td style="padding: 8px; text-align: left;"><strong>Grand Total:</strong></td>
                    <td style="padding: 8px; text-align: right;"><strong>{{ currency() }}{{ number_format($order->total_amount, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>

        <div class="info-block">
            <div class="">
                <h3>COMMENTS OR SPECIAL INSTRUCTIONS:</h3>
                @if(isset($order->notes))
                    <p>{{ $order->notes }}</p>
                @endif
            </div>
        </div>
        <p>Warranty Info#: {{ ucfirst($order->warranty_info) }}<br>
        <p>Payment Method: {{ ucfirst($order->payment_method) }}<br>

        <div class="footer">
            <p>Need help? Email: {{ settings()->support_email }} | Phone: {{ settings()->phone_number }}</p>
            <p><strong>THANK YOU FOR YOUR BUSINESS!</strong></p>
            <p>© {{ now()->year }} {{ appName() }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
