<?php $__env->startComponent('mail::message'); ?>
# Order Confirmation - #<?php echo e($order->order_number); ?>


<?php $customer = null ?>
<?php if(isset($order->shipping) && !empty($order->shipping)): ?>
    <?php $customer = $order->shipping ?>
<?php endif; ?>

Hi <?php echo e($customer->first_name ?? ''); ?> <?php echo e($customer->last_name ?? ''); ?>   ,

Thank you for your order! Here are your order details:

**Order ID:** <?php echo e($order->order_number); ?>  
**Order Date:** <?php echo e($order->created_at->format('d M Y')); ?>  
**Tax Amount:** $<?php echo e(number_format($order->tax, 2)); ?>  
**Shipping Amount:** $<?php echo e(number_format($order->shipping_cost, 2)); ?>  
**Total Amount:** $<?php echo e(number_format($order->total, 2)); ?>


<?php $__env->startComponent('mail::table'); ?>
| Product       | Qty | Price   | SubTotal  |
| ------------- |:---:| -------:|----------:|
<?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
| <?php echo e($item->product->mpn); ?> <?php echo e($item->product->title); ?> | <?php echo e($item->quantity); ?> | $<?php echo e(number_format($item->unit_price, 2)); ?> |$<?php echo e(number_format($item->sub_total, 2)); ?> |
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php echo $__env->renderComponent(); ?>

<?php $__env->startComponent('mail::button', ['url' => env('FRONTEND_BASE_URL').'/track-order']); ?>
View Your Order
<?php echo $__env->renderComponent(); ?>

If you have any questions, feel free to contact us.

Thanks,<br>
<?php echo e(config('app.name')); ?>

<?php echo $__env->renderComponent(); ?>
<?php /**PATH C:\xampp\htdocs\solid-dis-direct\backend\resources\views/emails/order/customer.blade.php ENDPATH**/ ?>