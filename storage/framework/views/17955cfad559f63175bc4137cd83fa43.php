<table class="table table-flush-spacing">
    <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td class="text-nowrap fw-semibold"><?php echo e($field['label'] ?? ucfirst($name)); ?></td>
            <td>
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
                    <?php if($name=='brand' && isset($model->hasBrand) && !empty($model->hasBrand)): ?>
                        <?php echo e($model->hasBrand->name ?? '-'); ?>

                    <?php elseif($name=='category' && isset($model->categories) && !empty($model->categories)): ?>                        
                    <?php echo $model->categories->pluck('name')->implode('<span class="highlight-arrow"> &rarr; </span>'); ?>

                    <?php elseif($name=='unit' && isset($model->hasUnit) && !empty($model->hasUnit)): ?>
                        <?php echo e($model->hasUnit->name ?? '-'); ?>

                    <?php elseif($name=='tax_type' && isset($model->hasTaxType) && !empty($model->hasTaxType)): ?>
                        <?php echo e($model->hasTaxType->name ?? '-'); ?>

                    <?php elseif($name=='condition' && isset($model->hasProductCondition) && !empty($model->hasProductCondition)): ?>
                        <?php echo e($model->hasProductCondition->name ?? '-'); ?>

                    <?php elseif($name=='unit_price' || $name=='discount_price'): ?>
                        <?php echo e(currency()); ?><?php echo $field['value'] ?? '-'; ?>

                    <?php else: ?>
                        <?php echo $field['value'] ?? '-'; ?>

                    <?php endif; ?>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php if(isset($model->hasProductImages) && !empty($model->hasProductImages)): ?>
        <tr>
            <td class="text-nowrap fw-semibold" colspan="2">Additional Images</td>
        </tr>
        <tr>
            <td colspan="2">
                <?php if(isset($model->hasProductImages) && !empty($model->hasProductImages)): ?>
                    <div id="existing-images" style="margin-top: 1rem; display: flex; gap: 10px; flex-wrap: wrap;">
                        <?php $__currentLoopData = $model->hasProductImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $productImage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="preview-wrapper" data-id="<?php echo e($productImage->id); ?>">
                                <img src="<?php echo e(asset('storage/'.$productImage->image)); ?>" width="80" height="80" class="preview-image zoomable" alt="">
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            </td>
        </tr>
    <?php endif; ?>
</table>
<?php /**PATH C:\xampp\htdocs\solid-dis-direct\backend\resources\views/admin/products/show_content.blade.php ENDPATH**/ ?>