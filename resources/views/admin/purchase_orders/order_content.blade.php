<div class="invoice-box">
    <div class="print-top-spacing"></div>
    <div class="invoice-print-area">
        <div class="no-print" style="text-align: right; margin-bottom: 20px;">
            <a href="{{ route('download.purchaseOrder.invoice', $order->id) }}" style="padding: 8px 16px; font-size: 14px; background-color: #036; color: white; border: none; border-radius: 4px; cursor: pointer;">
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
                    <p style="margin: 4px 0;"><strong>Billing Address:</strong><br /> {{ settings()->address }}</p>
                </div>
            </div>

            <div class="invoice-info" style="width: 50%; text-align: right;">
                <h2 style="margin: 0;">PURCHASE ORDER</h2>
                <p style="margin: 5px 0;"><strong>PO. NUMBER #:</strong> {{ $order->po_number }}</p>
                <p style="margin: 0;"><strong>REF. ID #: </strong> {{ $order->order_number }}</p>
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
                <h3>SHIPPING ADDRESS: </h3>
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
                    <td style="padding: 8px; text-align: right;">{{ currency() }}{{ number_format($order->sub_total, 2) }}</td>
                </tr>
                <tr>
                    <td style="border: none" colspan="4"></td>
                    <td style="padding: 8px; text-align: left;"><strong>Tax:</strong></td>
                    <td style="padding: 8px; text-align: right;">{{ currency() }}{{ number_format($order->tax, 2) }}</td>
                </tr>
                <tr>
                    <td style="border: none" colspan="4"></td>
                    <td style="padding: 8px; text-align: left;"><strong>Shipping:</strong></td>
                    <td style="padding: 8px; text-align: right;">{{ currency() }}{{ number_format($order->shipping_charges, 2) }}</td>
                </tr>
                <tr>
                    <td style="border: none" colspan="4"></td>
                    <td style="padding: 8px; text-align: left;"><strong>Grand Total:</strong></td>
                    <td style="padding: 8px; text-align: right;"><strong>{{ currency() }}{{ number_format($order->total_amount, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>

        <div style="clear: both;"></div>

        <div class="info-block">
            <div class="">
                <h3>COMMENTS OR SPECIAL INSTRUCTIONS:</h3>
                @if(isset($order->notes))
                    <p>{{ $order->notes }}</p>
                @endif
            </div>
        </div>
        <p>Warranty Info#:{{ ucfirst($order->warranty_info) }}<br>
        <p>Payment Method:{{ ucfirst($order->payment_method) }}<br>

        <div class="footer">
            <p>Need help? Email: {{ settings()->support_email }} | Phone: {{ settings()->phone_number }}</p>
            <p><strong>THANK YOU FOR YOUR BUSINESS!</strong></p>
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
