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

# New Support Request

**Name:** <?php echo e($data['name']); ?>  
**Email:** <?php echo e($data['email']); ?>  
**Phone:** <?php echo e($data['phone']); ?>  
**Subject:** <?php echo e($data['subject']); ?>


---

**Message:**  
<?php echo e($data['message']); ?>


---

Thanks,  
<?php echo e(config('app.name')); ?>


<?php echo $__env->renderComponent(); ?>
<?php /**PATH C:\xampp\htdocs\Solid-Disk-Direct\Solid-Disk-Direct\resources\views/emails/contact-support.blade.php ENDPATH**/ ?>