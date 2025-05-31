<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; margin: 0; padding: 20px; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; }
        table { width: 100%; line-height: inherit; text-align: left; border-collapse: collapse; }
        table th, table td { padding: 8px; border-bottom: 1px solid #eee; }
        .total-row td { font-weight: bold; }
        .heading { background: #eee; font-weight: bold; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <h2>Order Invoice</h2>
        <p><strong>Invoice #:</strong> {{ $order->order_number }}<br>
        <strong>Shop Name:</strong> Solid Disk Direct<br>
        <strong>Date:</strong> {{ $order->created_at }}</p>

        <h3>Shipping to</h3>
        {{-- <p>{{ $shipping_name }}<br>{{ $shipping_email }}<br>{{ $shipping_phone }}<br>{{ $shipping_address }}</p>

        <h3>Billing Address</h3>
        <p>{{ $billing_name }}<br>{{ $billing_email }}<br>{{ $billing_phone }}<br>{{ $billing_address }}</p> --}}

        <table>
            <tr class="heading">
                <th>SL</th>
                <th>Item Description</th>
                <th>Unit Price</th>
                <th>Qty</th>
                <th>Total</th>
            </tr>
            @foreach ($order->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['description'] }}</td>
                    <td>${{ number_format($item['unit_price'], 2) }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td>${{ number_format($item['total'], 2) }}</td>
                </tr>
            @endforeach
        </table>

        <p><strong>Payment Details:</strong> {{ $order->payment_status }}, {{ $order->created_at }}</p>
        <p><strong>Delivery Info:</strong> <br>Tracking Id: </p>

        <table>
            <tr><td>Sub Total</td><td>${{ number_format($order->subtotal, 2) }}</td></tr>
            <tr><td>Tax</td><td>${{ number_format($order->tax, 2) }}</td></tr>
            <tr><td>Shipping</td><td>${{ number_format($order->shipping_cost, 2) }}</td></tr>
            <tr><td>Promotion Discount</td><td>-$0</td></tr>
            {{-- <tr><td>Discount On Product</td><td>-${{ number_format($product_discount, 2) }}</td></tr> --}}
            <tr class="total-row"><td>Total</td><td>${{ number_format($order->total, 2) }}</td></tr>
        </table>

        <p>If you need assistance, email: support@soliddiskdirect.com<br>
        Phone: +18722530966<br>
        Website: https://soliddiskdirect.com</p>
        <p style="text-align:center;">All Copyright Reserved © {{ now()->year }} Solid Disk Direct</p>
    </div>
</body>
</html>