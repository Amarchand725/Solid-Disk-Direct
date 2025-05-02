<table class="table table-flush-spacing">
    <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td class="text-nowrap fw-semibold"><?php echo e($field['label'] ?? ucfirst($name)); ?></td>
            <td>
                <?php if($field['type'] === 'file'): ?>
                    <?php if(!empty($field['value'])): ?>
                        <img src="<?php echo e(asset('storage/' . $field['value'])); ?>" width="80" class="zoomable">
                    <?php else: ?>
                        -
                    <?php endif; ?>
                <?php elseif($name === 'status'): ?>
                    <span class="badge bg-label-<?php echo e($model->status ? 'success' : 'danger'); ?>">
                        <?php echo e($model->status ? 'Active' : 'Deactive'); ?>

                    </span>
                <?php else: ?>
                    <?php echo e($field['value']); ?>

                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</table>
<?php /**PATH C:\xampp\htdocs\Solid-Disk-Direct\Solid-Disk-Direct\resources\views/admin/blogs/show_content.blade.php ENDPATH**/ ?>