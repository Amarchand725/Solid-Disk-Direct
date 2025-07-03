<div class="invoice-box">
    <div class="print-top-spacing"></div>
    <div class="invoice-print-area">
        <div class="no-print" style="text-align: right; margin-bottom: 20px;">
            <a href="<?php echo e(route('download.purchaseOrder.invoice', $order->id)); ?>" style="padding: 8px 16px; font-size: 14px; background-color: #036; color: white; border: none; border-radius: 4px; cursor: pointer;">
                Print Invoice
            </a>
        </div>

        <div class="header mt-4">
            <div class="logo" style="width: 50%;">
                <?php if(isset(settings()->black_logo) && !empty(settings()->black_logo)): ?>
                    <img src="<?php echo e(asset('storage').'/'.settings()->black_logo); ?>" style="width: 100%; max-width: 180px;" alt="<?php echo e(settings()->name); ?>" />
                <?php else: ?>
                    <img src="<?php echo e(asset('storage/images/default.png')); ?>" style="width: 100%; max-width: 180px;" alt="Default" />
                <?php endif; ?>
                <div style="margin-top: 10px; font-size: 13px; color: #555; text-decoration: none;">
                    <p style="margin: 4px 0;"><strong>Billing Address:</strong><br /> <?php echo e(settings()->address); ?></p>
                </div>
            </div>

            <div class="invoice-info" style="width: 50%; text-align: right;">
                <h2 style="margin: 0;">PURCHASE ORDER</h2>
                <p style="margin: 5px 0;"><strong>PO. NUMBER #:</strong> <?php echo e($order->po_number); ?></p>
                <p style="margin: 0;"><strong>REF. ID #: </strong> <?php echo e($order->order_number); ?></p>
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
                <h3>SHIPPING ADDRESS: </h3>
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
                    <th style="background-color: #036; color: #ffffff; padding: 8px;">Item Description</th>
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
                        <td style="padding: 8px;"><?php echo e($item->product->title ?? '-'); ?></td>
                        <td style="padding: 8px; text-align: right;"><?php echo e($item->quantity); ?></td>
                        <td style="padding: 8px; text-align: right;"><?php echo e(currency()); ?><?php echo e(number_format($item->unit_price, 2)); ?></td>
                        <td style="padding: 8px; text-align: right;"><?php echo e(currency()); ?><?php echo e(number_format($item->sub_total, 2)); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <!-- Optional spacing row -->
                <tr><td style="border: none" colspan="6" style="height: 20px;"></td></tr>

                <!-- Order Summary rows (aligned under Unit Price & Total) -->
                <tr>
                    <td style="border: none" colspan="4"></td>
                    <td style="padding: 8px; text-align: left;"><strong>Sub Total:</strong></td>
                    <td style="padding: 8px; text-align: right;"><?php echo e(currency()); ?><?php echo e(number_format($order->sub_total, 2)); ?></td>
                </tr>
                <tr>
                    <td style="border: none" colspan="4"></td>
                    <td style="padding: 8px; text-align: left;"><strong>Tax:</strong></td>
                    <td style="padding: 8px; text-align: right;"><?php echo e(currency()); ?><?php echo e(number_format($order->tax, 2)); ?></td>
                </tr>
                <tr>
                    <td style="border: none" colspan="4"></td>
                    <td style="padding: 8px; text-align: left;"><strong>Shipping:</strong></td>
                    <td style="padding: 8px; text-align: right;"><?php echo e(currency()); ?><?php echo e(number_format($order->shipping_charges, 2)); ?></td>
                </tr>
                <tr>
                    <td style="border: none" colspan="4"></td>
                    <td style="padding: 8px; text-align: left;"><strong>Grand Total:</strong></td>
                    <td style="padding: 8px; text-align: right;"><strong><?php echo e(currency()); ?><?php echo e(number_format($order->total_amount, 2)); ?></strong></td>
                </tr>
            </tbody>
        </table>

        <div style="clear: both;"></div>

        <div class="info-block">
            <div class="">
                <h3>COMMENTS OR SPECIAL INSTRUCTIONS:</h3>
                <?php if(isset($order->notes)): ?>
                    <p><?php echo e($order->notes); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <p>Warranty Info#:<?php echo e(ucfirst($order->warranty_info)); ?><br>
        <p>Payment Method:<?php echo e(ucfirst($order->payment_method)); ?><br>

        <div class="footer">
            <p>Need help? Email: <?php echo e(settings()->support_email); ?> | Phone: <?php echo e(settings()->phone_number); ?></p>
            <p><strong>THANK YOU FOR YOUR BUSINESS!</strong></p>
            <p>© <?php echo e(now()->year); ?> <?php echo e(appName()); ?>. All rights reserved.</p>
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
<?php /**PATH C:\xampp\htdocs\Solid-Disk-Direct\Solid-Disk-Direct\resources\views/admin/purchase_orders/order_content.blade.php ENDPATH**/ ?>