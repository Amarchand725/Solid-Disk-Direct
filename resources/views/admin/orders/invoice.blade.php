<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->id }}</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        .total { font-weight: bold; }
    </style>
</head>
<body>
    <h2>Invoice - Order #{{ $order->id }}</h2>
    <p><strong>Customer:</strong> {{ $order->customer_name }}</p>
    <p><strong>Email:</strong> {{ $order->customer_email }}</p>
    <p><strong>Date:</strong> {{ $order->created_at->format('d M Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>${{ number_format($item->price, 2) }}</td>
                </tr>
            @endforeach
            <tr class="total">
                <td colspan="2">Total</td>
                <td>${{ number_format($order->total, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
