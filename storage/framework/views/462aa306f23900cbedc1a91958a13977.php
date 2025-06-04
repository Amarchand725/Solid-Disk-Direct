<?php $__env->startComponent('mail::message'); ?>
# Order Confirmation - #<?php echo e($order->id); ?>


Hi <?php echo e($order->customer_name); ?>,

Thank you for your order! Here are your order details:

**Order ID:** <?php echo e($order->id); ?>  
**Order Date:** <?php echo e($order->created_at->format('d M Y')); ?>  
**Total Amount:** $<?php echo e(number_format($order->total, 2)); ?>


<?php $__env->startComponent('mail::table'); ?>
| Product       | Qty | Price   |
| ------------- |:---:| -------:|
<?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
| <?php echo e($item->product_name); ?> | <?php echo e($item->quantity); ?> | $<?php echo e(number_format($item->price, 2)); ?> |
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php echo $__env->renderComponent(); ?>

<?php $__env->startComponent('mail::button', ['url' => url('/orders/'.$order->order_number)]); ?>
View Your Order
<?php echo $__env->renderComponent(); ?>

If you have any questions, feel free to contact us.

Thanks,<br>
<?php echo e(config('app.name')); ?>

<?php echo $__env->renderComponent(); ?>
<?php /**PATH C:\xampp\htdocs\Solid-Disk-Direct\Solid-Disk-Direct\resources\views/emails/order/customer.blade.php ENDPATH**/ ?>