<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #<?php echo e($order->order_number); ?></title>
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
        <p><strong>Invoice #:</strong> <?php echo e($order->order_number); ?><br>
        <strong>Shop Name:</strong> Solid Disk Direct<br>
        <strong>Date:</strong> <?php echo e($order->created_at); ?></p>

        <h3>Shipping to</h3>
        

        <table>
            <tr class="heading">
                <th>SL</th>
                <th>Item Description</th>
                <th>Unit Price</th>
                <th>Qty</th>
                <th>Total</th>
            </tr>
            <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($index + 1); ?></td>
                    <td><?php echo e($item['description']); ?></td>
                    <td>$<?php echo e(number_format($item['unit_price'], 2)); ?></td>
                    <td><?php echo e($item['quantity']); ?></td>
                    <td>$<?php echo e(number_format($item['total'], 2)); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </table>

        <p><strong>Payment Details:</strong> <?php echo e($order->payment_status); ?>, <?php echo e($order->created_at); ?></p>
        <p><strong>Delivery Info:</strong> <br>Tracking Id: </p>

        <table>
            <tr><td>Sub Total</td><td>$<?php echo e(number_format($order->subtotal, 2)); ?></td></tr>
            <tr><td>Tax</td><td>$<?php echo e(number_format($order->tax, 2)); ?></td></tr>
            <tr><td>Shipping</td><td>$<?php echo e(number_format($order->shipping_cost, 2)); ?></td></tr>
            <tr><td>Promotion Discount</td><td>-$0</td></tr>
            
            <tr class="total-row"><td>Total</td><td>$<?php echo e(number_format($order->total, 2)); ?></td></tr>
        </table>

        <p>If you need assistance, email: support@soliddiskdirect.com<br>
        Phone: +18722530966<br>
        Website: https://soliddiskdirect.com</p>
        <p style="text-align:center;">All Copyright Reserved © <?php echo e(now()->year); ?> Solid Disk Direct</p>
    </div>
</body>
</html><?php /**PATH C:\xampp\htdocs\Solid-Disk-Direct\Solid-Disk-Direct\resources\views/admin/orders/invoice.blade.php ENDPATH**/ ?>