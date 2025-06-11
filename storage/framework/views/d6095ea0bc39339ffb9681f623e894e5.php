<td>
    <ul class="d-flex flex-wrap list-unstyled m-0" style="max-width: 500px; overflow-x: auto;">
        <?php $__currentLoopData = SubPermissions($model->label); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="me-1 my-1">
                <span class="badge bg-label-primary"><?php echo e($label->name); ?></span>
            </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
</td>
<?php /**PATH C:\xampp\htdocs\Solid-Disk-Direct\Solid-Disk-Direct\resources\views/admin/permissions/permissions.blade.php ENDPATH**/ ?>