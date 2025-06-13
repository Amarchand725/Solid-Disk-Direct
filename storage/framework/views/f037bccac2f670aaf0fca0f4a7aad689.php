<?php $__env->startComponent('mail::message'); ?>
<table width="100%" style="text-align: center; margin-bottom: 20px;">
    <tr>
        <td>
            <?php if(isset(settings()->black_logo) && !empty(settings()->black_logo)): ?>
                <img src="<?php echo e(asset('storage').'/'.settings()->black_logo); ?>" style="height: 40px;" alt="<?php echo e(settings()->name); ?>" />
            <?php else: ?>
                <img src="<?php echo e(asset('storage/images/default.png')); ?>" style="height: 40px;" alt="Default" />
            <?php endif; ?>
        </td>
    </tr>
</table>
# Thank You for Your Purchase, <?php echo e($customerName); ?>!

We hope you're enjoying your recent purchase from **<?php echo e($storeName); ?>**.

We’d really appreciate it if you could take a moment to leave a quick review of your experience.

<?php $__env->startComponent('mail::button', ['url' => $reviewLink]); ?>
Leave a Review
<?php echo $__env->renderComponent(); ?>

If you have any questions, feel free to reply to this email.  
Thanks again for shopping with us!

Best regards,  
**The <?php echo e($storeName); ?> Team**

<?php echo $__env->renderComponent(); ?>
<?php /**PATH C:\xampp\htdocs\Solid-Disk-Direct\Solid-Disk-Direct\resources\views/emails/order/review.blade.php ENDPATH**/ ?>