<?php $__env->startComponent('mail::message'); ?>
# New Order Received

**Order ID:** #<?php echo e($order->order_number); ?>  
**Customer:** <?php echo e($order->customer_name); ?>  
**Email:** <?php echo e($order->customer_email); ?>  
**Total:** $<?php echo e(number_format($order->total, 2)); ?>


<?php $__env->startComponent('mail::button', ['url' => url('/admin/orders/'.$order->id)]); ?>
View Order in Admin Panel
<?php echo $__env->renderComponent(); ?>

<?php echo $__env->renderComponent(); ?>
<?php /**PATH C:\xampp\htdocs\Solid-Disk-Direct\Solid-Disk-Direct\resources\views/emails/order/admin.blade.php ENDPATH**/ ?>