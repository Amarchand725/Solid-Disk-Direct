<u>
<?php $__currentLoopData = SubPermissions($model->label); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <li><span class="badge bg-label-primary me-1 my-1"> <?php echo e($label->name); ?></span></li>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</u>
<?php /**PATH C:\xampp\htdocs\solid-dis-direct\backend\resources\views/admin/permissions/permissions.blade.php ENDPATH**/ ?>