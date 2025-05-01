<table class="table table-flush-spacing">
    <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td class="text-nowrap fw-semibold"><?php echo e($field['label'] ?? ucfirst($name)); ?></td>
            <td>
                <?php if($field['type'] === 'file'): ?>
                    <?php if(!empty($field['value'])): ?>
                        <img src="<?php echo e(asset('storage/' . $field['value'])); ?>" width="80">
                    <?php endif; ?>
                <?php elseif($name === 'status'): ?>
                    <span class="badge bg-label-<?php echo e($model->status ? 'success' : 'danger'); ?>">
                        <?php echo e($model->status ? 'Active' : 'Deactive'); ?>

                    </span>
                <?php elseif($name === 'fields'): ?>
                    <?php $tableFields = json_decode($field['value'], true) ?> 
                    <table class="table">
                        <tr>
                            <th>Field Name</th>
                            <th>Data Type</th>
                            <th>Input Type</th>
                        </tr>
                        <?php if(isset($tableFields) && !empty($tableFields)): ?>
                            <?php $__currentLoopData = $tableFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tableKey=>$tableField): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e(ucfirst($tableField['field']) ?? ''); ?></td>
                                    <td><?php echo e($tableField['type'] ?? ''); ?></td>
                                    <td><?php echo e($tableField['input_type'] ?? ''); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </table>
                <?php else: ?>
                    <?php if($name=='menu_group' && isset($model->hasMenuGroup) && !empty($model->hasMenuGroup)): ?>
                        <?php echo e($model->hasMenuGroup->menu ?? '-'); ?>

                    <?php elseif($name=='icon'): ?>
                        <i class="menu-icon tf-icons <?php echo e($model->icon ?? 'ti ti-smart-home'); ?>"></i>
                    <?php else: ?>
                        <?php echo $field['value'] ?? '-'; ?>

                    <?php endif; ?>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</table>
<?php /**PATH C:\xampp\htdocs\solid-dis-direct\backend\resources\views/admin/menus/show_content.blade.php ENDPATH**/ ?>