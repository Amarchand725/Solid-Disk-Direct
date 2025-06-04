<?php $__env->startComponent('mail::message'); ?>
# New Order Received

<?php $customer = null ?>
<?php if(isset($order->shipping) && !empty($order->shipping)): ?>
    <?php $customer = $order->shipping ?>
<?php endif; ?>

**Order ID:** #<?php echo e($order->order_number); ?>  
**Customer:** <?php echo e($customer->first_name ?? ''); ?> <?php echo e($customer->last_name ?? ''); ?>  
**Email:** <?php echo e($customer->email ?? ''); ?>  
**Total:** $<?php echo e(number_format($order->total, 2)); ?>


<?php $__env->startComponent('mail::button', ['url' => url('/admin/orders/'.$order->id)]); ?>
View Order in Admin Panel
<?php echo $__env->renderComponent(); ?>

<?php echo $__env->renderComponent(); ?>
<?php /**PATH C:\xampp\htdocs\solid-dis-direct\backend\resources\views/emails/order/admin.blade.php ENDPATH**/ ?>