<table class="table table-flush-spacing" style="table-layout: fixed; width: 100%;">
    <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td class="text-nowrap fw-semibold" style="width: 100px;"><?php echo e($field['label'] ?? ucfirst($name)); ?></td>
            <td style="max-width: 200px; word-break: break-word; white-space: normal; overflow-wrap: break-word; overflow: hidden;">
                <?php if($field['type'] === 'file'): ?>
                    <?php if(!empty($field['value'])): ?>
                        <img src="<?php echo e(asset('storage/' . $field['value'])); ?>" width="80">
                    <?php else: ?>
                        -
                    <?php endif; ?>
                <?php elseif($name === 'status'): ?>
                    <span class="badge bg-label-<?php echo e($model->status ? 'success' : 'danger'); ?>">
                        <?php echo e($model->status ? 'Active' : 'Deactive'); ?>

                    </span>
                <?php elseif($field['type'] === 'checkbox'): ?>
                    <span class="badge bg-label-<?php echo e($model->status ? 'success' : 'danger'); ?>">
                        <?php echo e($model->status ? 'Yes' : 'No'); ?>

                    </span>
                <?php else: ?>
                    <?php if($name=='parent' && isset($model->parents) && !empty($model->parents)): ?>                        
                        <?php echo $model->parents->pluck('name')->implode('<span class="highlight-arrow"> &rarr; </span>'); ?>

                    <?php else: ?>
                        <?php echo $field['value']; ?>

                    <?php endif; ?>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</table><?php /**PATH C:\xampp\htdocs\Solid-Disk-Direct\Solid-Disk-Direct\resources\views/admin/categories/show_content.blade.php ENDPATH**/ ?>