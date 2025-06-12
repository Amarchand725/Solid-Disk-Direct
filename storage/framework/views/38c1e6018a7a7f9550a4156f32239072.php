<h5>Order #<?php echo e($order->order_number); ?></h5>
<p style="margin: 0;"><strong>Status:</strong> 
    <?php
        $statusClass = orderStatus()[$order->order_status] ?? 'secondary';
    ?>
    <span class="badge bg-<?php echo e($statusClass); ?>">
        <?php echo e(ucfirst($order->order_status)); ?>

    </span>
</p>

<p style="margin: 0;"><strong>Total:</strong> <?php echo e(currency()); ?><?php echo e(number_format($order->total, 2)); ?></p>
<p style="margin: 0;"><strong>Date:</strong> <?php echo e($order->created_at->format('d M Y')); ?></p>
<p><strong>Payment Method:</strong> <span class="badge bg-info"><?php echo e(ucfirst($order->payment_method)); ?></span></p>
<p><strong>Payment Status:</strong> <span <?php if($order->payment_status=='paid'): ?> class="badge bg-success" <?php else: ?> class="badge bg-danger" <?php endif; ?>><?php echo e(ucfirst($order->payment_status)); ?></span></p>

<hr>
<h6>Customer Info</h6>
<?php if(isset($order->customer) && !empty($order->customer)): ?>
    <p><?php echo e($order->customer->first_name ?? ''); ?> <?php echo e($order->customer->last_name ?? ''); ?> (<?php echo e($order->customer->email); ?>)</p>
<?php else: ?>
    <p>Not Available</p>
<?php endif; ?>

<hr>
<div style="display: flex; gap: 20px; justify-content: space-between;">
    
    <!-- Shipping Address -->
    <div style="flex: 1;">
        <h6>Shipping Address</h6>
        <?php if(isset($order->shipping) && !empty($order->shipping)): ?>
            <p>
                <?php echo e($order->shipping->first_name ?? '-'); ?> <?php echo e($order->shipping->last_name ?? ''); ?><br>
                <?php echo e($order->shipping->address ?? '-'); ?><br>
                <?php if(isset($order->shipping->getState)): ?>
                    <?php echo e($order->shipping->getState->name ?? '-'); ?>,
                <?php endif; ?>
                <?php if(isset($order->shipping->getCity)): ?>
                    <?php echo e($order->shipping->getCity->name ?? '-'); ?>,
                <?php endif; ?>
                <?php echo e($order->shipping->zip ?? '-'); ?><br>
                <?php echo e($order->shipping->email ?? '-'); ?><br>
                <?php echo e($order->shipping->phone ?? '-'); ?>

            </p>
        <?php else: ?>
            <p>Not Found Shipping Address</p>
        <?php endif; ?>
    </div>

    <!-- Billing Address -->
    <div style="flex: 1;">
        <h6>Billing Address</h6>
        <?php if(isset($order->billing) && !empty($order->billing)): ?>
            <p>
                <?php echo e($order->billing->first_name ?? '-'); ?> <?php echo e($order->billing->last_name ?? ''); ?><br>
                <?php echo e($order->billing->address ?? '-'); ?><br>
                <?php if(isset($order->billing->getState)): ?>
                    <?php echo e($order->billing->getState->name ?? '-'); ?>,
                <?php endif; ?>
                <?php if(isset($order->billing->getCity)): ?>
                    <?php echo e($order->billing->getCity->name ?? '-'); ?>,
                <?php endif; ?>
                <?php echo e($order->billing->zip ?? '-'); ?><br>
                <?php echo e($order->billing->email ?? '-'); ?><br>
                <?php echo e($order->billing->phone ?? '-'); ?>

            </p>
        <?php else: ?>
            <p>Not Found Billing Address</p>
        <?php endif; ?>
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
        <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><?php echo e($item->product->mpn ?? '-'); ?></td>
            <td><?php echo e($item->product->title ?? '-'); ?></td>
            <td><?php echo e($item->quantity); ?></td>
            <td><?php echo e(currency()); ?><?php echo e(number_format($item->unit_price, 2)); ?></td>
            <td><?php echo e(currency()); ?><?php echo e(number_format($item->sub_total, 2)); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>

<div style="display: flex; justify-content: flex-end; margin-top: -10px;">
    <table style="width: 300px; border-collapse: collapse;">
        <tr>
            <td style="padding: 8px; border: 1px solid #eee;">Sub Total:</td>
            <td style="padding: 8px; border: 1px solid #eee;">
                <?php echo e(currency()); ?><?php echo e(number_format($order->subtotal, 2)); ?>

            </td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #eee;">Tax:</td>
            <td style="padding: 8px; border: 1px solid #eee;">
                <?php echo e(currency()); ?><?php echo e(number_format($order->tax, 2)); ?>

            </td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #eee;">Shipping:</td>
            <td style="padding: 8px; border: 1px solid #eee;">
                <?php echo e(currency()); ?><?php echo e(number_format($order->shipping_cost, 2)); ?>

            </td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #eee;">Promotion Discount:</td>
            <td style="padding: 8px; border: 1px solid #eee;">
                -<?php echo e(currency()); ?>0.00
            </td>
        </tr>
        <tr style="font-weight: bold;">
            <td style="padding: 10px; border: 1px solid #eee;">Total:</td>
            <td style="padding: 10px; border: 1px solid #eee;">
                <?php echo e(currency()); ?><?php echo e(number_format($order->total, 2)); ?>

            </td>
        </tr>
    </table>
</div>

<div style="clear: both;"></div>

<?php /**PATH C:\xampp\htdocs\Solid-Disk-Direct\Solid-Disk-Direct\resources\views/admin/orders/order_content.blade.php ENDPATH**/ ?>