<?php echo method_field('PUT'); ?>
<?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php if($name != 'created_at'): ?>
        <div class="col-12 mb-3">
            <label class="form-label" for="<?php echo e($name); ?>">
                <?php echo e($field['label'] ?? ucfirst($name)); ?>

                
                <?php if(isset($field['required']) && $field['required']): ?>
                    <?php if(isset($field['type']) && $field['type'] == 'file' && empty($field['value'])): ?>
                        <span class="text-danger">*</span>  <!-- Display * if file type and value is empty -->
                    <?php elseif($field['type'] != 'file'): ?>
                        <span class="text-danger">*</span>  <!-- Display * if required and not file type -->
                    <?php endif; ?>
                <?php endif; ?>

            </label>

            <?php if(isset($field['type']) && $field['type'] === 'select'): ?>
                <?php if($name=='parent_id'): ?>
                    <select id="<?php echo e($name); ?>" name="<?php echo e($name); ?>" class="form-control">
                        <option value="" selected>Select parent category</option>
                        <?php $__currentLoopData = $parent_categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $parent_category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($parent_category->id); ?>">
                                <?php echo e($parent_category->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                <?php else: ?>
                    <select id="<?php echo e($name); ?>" name="<?php echo e($name); ?>" class="form-control">
                        <?php $__currentLoopData = $field['options'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>  <!-- Safely handle 'options' -->
                            <option value="<?php echo e($key); ?>" <?php echo e($model->status == $key ? 'selected' : ''); ?>>
                                <?php echo e($option); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                <?php endif; ?>
            <?php elseif(isset($field['type']) && $field['type'] === 'textarea'): ?>
                <textarea id="<?php echo e($name); ?>" name="<?php echo e($name); ?>" class="form-control" placeholder="<?php echo e($field['placeholder'] ?? ''); ?>"><?php echo e(old($name, $field['value'] ?? '')); ?></textarea>
            <?php elseif(isset($field['type']) && $field['type'] === 'file'): ?>
                <input 
                    type="<?php echo e($field['type'] ?? 'file'); ?>" 
                    id="file-uploader" 
                    name="<?php echo e($name); ?>" 
                    accept="<?php echo e(isset($field['accept']) ? $field['accept'] : ''); ?>"
                    class="form-control uploader" 
                    autofocus
                />

                <span id="preview-<?php echo e($name); ?>">
                    <?php if(!empty($field['value'])): ?>
                        <img src="<?php echo e(asset('storage/' . $field['value'])); ?>" style="width:60px; height:50px" alt="Avatar" class="img-avatar zoomable">
                    <?php endif; ?>
                </span>
            <?php else: ?>
                <input 
                    type="<?php echo e($field['type'] ?? 'text'); ?>" 
                    id="<?php echo e($name); ?>" 
                    name="<?php echo e($name); ?>" 
                    class="form-control" 
                    placeholder="<?php echo e($field['placeholder'] ?? ''); ?>" 
                    value="<?php echo e(old($name, $field['value'] ?? '')); ?>" 
                    autofocus
                />
            <?php endif; ?>

            <span id="<?php echo e($name); ?>_error" class="text-danger error"></span>
        </div>
    <?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<script>
    $('select').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),
        });
    });
</script><?php /**PATH C:\xampp\htdocs\Solid-Disk-Direct\Solid-Disk-Direct\resources\views/admin/banners/edit_content.blade.php ENDPATH**/ ?>