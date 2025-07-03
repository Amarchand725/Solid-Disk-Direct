<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order Invoice #<?php echo e($order->po_number); ?></title>
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
                <?php
                    $logoPath = settings()->black_logo 
                        ? public_path('storage/' . settings()->black_logo) 
                        : public_path('storage/images/default.png');
                ?>
                <img src="<?php echo e($logoPath); ?>" style="width: 100%; max-width: 180px;" alt="Logo">
                <div style="margin-top: 10px; font-size: 13px; color: #555;">
                    <p><strong>Billing Address:</strong><br /> <?php echo e(settings()->address); ?></p>
                </div>
            </div>

            <div class="invoice-info">
                <h2 style="margin: 0 0 5px 0;">PURCHASE ORDER</h2>
                <p style="margin: 0;"><strong>PO. NUMBER #:</strong> <?php echo e($order->po_number); ?></p>
                <p style="margin: 0;"><strong>REF. ID #:</strong> <?php echo e($order->order_number); ?></p>
                <p style="margin: 0;"><strong>Date:</strong> <?php echo e($order->created_at->format('d M Y')); ?></p>
            </div>
        </div>

        <div class="info-block">
            <div class="info-box">
                <h3>VENDOR DETAILS:</h3>
                <?php if(isset($order->getVendor)): ?>
                    <p>
                        <?php echo e(!empty($order->getVendor->first_name) ? $order->getVendor->first_name : '-'); ?> <?php echo e($order->getVendor->last_name ?? ''); ?><br>
                        <?php echo e(!empty($order->getVendor->address) ? $order->getVendor->address. ',' : '-'); ?> <br />
                        <?php if(isset($order->getVendor->getState) && !empty($order->getVendor->getState->name)): ?>
                            <?php echo e($order->getVendor->getState->name ?? '-'); ?>, 
                        <?php endif; ?>
                        <?php if(isset($order->getVendor->getCity) && !empty($order->getVendor->getCity->name)): ?>
                            <?php echo e($order->getVendor->getCity->name ?? ''); ?>,
                        <?php endif; ?>
                        
                        <?php echo e(!empty($order->getVendor->zip) ? $order->getVendor->zip. ',' : ''); ?><br>
                        <?php echo e(!empty($order->getVendor->email) ? $order->getVendor->email : '-'); ?><br>
                        <?php echo e(!empty($order->getVendor->phone) ? $order->getVendor->phone : '-'); ?>

                    </p>
                <?php endif; ?>
            </div>

            <div class="info-box">
                <h3>SHIPPING ADDRESS:</h3>
                <?php if(isset($order->getOrder->shipping)): ?>
                    <p>
                        <?php echo e(!empty($order->getOrder->shipping->first_name) ? $order->getOrder->shipping->first_name : '-'); ?> <?php echo e($order->getOrder->shipping->last_name ?? ''); ?><br>
                        <?php echo e(!empty($order->getOrder->shipping->address) ? $order->getOrder->shipping->address. ',' : '-'); ?> <br />
                        <?php if(isset($order->getOrder->shipping->getState) && !empty($order->getOrder->shipping->getState->name)): ?>
                            <?php echo e($order->getOrder->shipping->getState->name ?? '-'); ?>, 
                        <?php endif; ?>
                        <?php if(isset($order->getOrder->shipping->getCity) && !empty($order->getOrder->shipping->getCity->name)): ?>
                            <?php echo e($order->getOrder->shipping->getCity->name ?? ''); ?>,
                        <?php endif; ?>
                        
                        <?php echo e(!empty($order->getOrder->shipping->zip) ? $order->getOrder->shipping->zip. ',' : ''); ?>

                        <br>
                        <?php echo e(!empty($order->getOrder->shipping->email) ? $order->getOrder->shipping->email : '-'); ?><br>
                        <?php echo e(!empty($order->getOrder->shipping->phone) ? $order->getOrder->shipping->phone : '-'); ?>

                    </p>
                <?php endif; ?>
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
                <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td style="padding: 8px;"><?php echo e($index + 1); ?></td>
                        <td style="padding: 8px;"><?php echo e($item->product->mpn ?? '-'); ?></td>
                        <td style="padding: 8px;"><?php echo $item->product->short_description ?? '-'; ?></td>
                        <td style="padding: 8px;"><?php echo e($item->product->condition ?? '-'); ?></td>
                        <td style="padding: 8px; text-align: right;"><?php echo e($item->quantity); ?></td>
                        <td style="padding: 8px; text-align: right;"><?php echo e(currency()); ?><?php echo e(number_format($item->unit_price, 2)); ?></td>
                        <td style="padding: 8px; text-align: right;"><?php echo e(currency()); ?><?php echo e(number_format($item->sub_total, 2)); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <tr>
                    <td style="border: 0; background: none; padding: 0;" colspan="5"></td>
                    <td style="padding: 8px; text-align: left;"><strong>Sub Total:</strong></td>
                    <td style="padding: 8px; text-align: right;"><?php echo e(currency()); ?><?php echo e(number_format($order->sub_total, 2)); ?></td>
                </tr>
                <tr>
                    <td style="border: 0; background: none; padding: 0;" colspan="5"></td>
                    <td style="padding: 8px; text-align: left;"><strong>Tax:</strong></td>
                    <td style="padding: 8px; text-align: right;"><?php echo e(currency()); ?><?php echo e(number_format($order->tax, 2)); ?></td>
                </tr>
                <tr>
                    <td style="border: 0; background: none; padding: 0;" colspan="5"></td>
                    <td style="padding: 8px; text-align: left;"><strong>Shipping:</strong></td>
                    <td style="padding: 8px; text-align: right;"><?php echo e(currency()); ?><?php echo e(number_format($order->shipping_charges, 2)); ?></td>
                </tr>
                <tr>
                    <td style="border: 0; background: none; padding: 0;" colspan="5"></td>
                    <td style="padding: 8px; text-align: left;"><strong>Grand Total:</strong></td>
                    <td style="padding: 8px; text-align: right;"><strong><?php echo e(currency()); ?><?php echo e(number_format($order->total_amount, 2)); ?></strong></td>
                </tr>
            </tbody>
        </table>

        <div class="info-block">
            <div class="">
                <h3>COMMENTS OR SPECIAL INSTRUCTIONS:</h3>
                <?php if(isset($order->notes)): ?>
                    <p><?php echo e($order->notes); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <p>Warranty Info#: <?php echo e(ucfirst($order->warranty_info)); ?><br>
        <p>Payment Method: <?php echo e(ucfirst($order->payment_method)); ?><br>

        <div class="footer">
            <p>Need help? Email: <?php echo e(settings()->support_email); ?> | Phone: <?php echo e(settings()->phone_number); ?></p>
            <p><strong>THANK YOU FOR YOUR BUSINESS!</strong></p>
            <p>© <?php echo e(now()->year); ?> <?php echo e(appName()); ?>. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Solid-Disk-Direct\Solid-Disk-Direct\resources\views/admin/purchase_orders/invoice_content.blade.php ENDPATH**/ ?>