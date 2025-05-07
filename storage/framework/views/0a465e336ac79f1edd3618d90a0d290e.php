<?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php if($name != 'created_at'): ?>
        <div class="col-12 mb-3">
            <label class="form-label" for="<?php echo e($name); ?>">
                <?php echo e($field['label'] ?? ucfirst($name)); ?>

                <?php if(isset($field['required']) && $field['required']): ?> 
                    <span class="text-danger">*</span>  <!-- Display * if required -->
                <?php endif; ?>
            </label>

            <?php if(isset($field['type']) && $field['type'] === 'select'): ?>
                <select id="<?php echo e($name); ?>" name="<?php echo e($name); ?>" class="form-control">
                    <?php $__currentLoopData = $field['options'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>  <!-- Safely handle 'options' -->
                        <option value="<?php echo e($key); ?>" <?php echo e(old($name, $field['value']) == $key ? 'selected' : ''); ?>>
                            <?php echo e($option); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            <?php elseif(isset($field['type']) && $field['type'] === 'textarea'): ?>
                <textarea id="<?php echo e($name); ?>" name="<?php echo e($name); ?>" class="form-control summernote" placeholder="<?php echo e($field['placeholder'] ?? ''); ?>"><?php echo e(old($name, $field['value'] ?? '')); ?></textarea>
            <?php elseif(isset($field['type']) && $field['type'] === 'file'): ?>
                <input 
                    type="<?php echo e($field['type'] ?? 'text'); ?>" 
                    id="file-uploader" 
                    name="<?php echo e($name); ?>" 
                    accept="<?php echo e(isset($field['accept']) ? $field['accept'] : ''); ?>" 
                    class="form-control uploader" 
                    placeholder="<?php echo e($field['placeholder'] ?? ''); ?>" 
                    value="<?php echo e(old($name, $field['value'] ?? '')); ?>" 
                    autofocus
                />

                <span id="preview-<?php echo e($name); ?>">
                    <?php if(!empty($field['value'])): ?>
                        <img src="<?php echo e(asset('storage/' . $field['value'])); ?>" style="width:60px; height:50px" alt="Avatar" class="img-avatar zoomable">
                    <?php endif; ?>
                </span>
            <?php elseif(isset($field['type']) && $field['type'] === 'checkbox'): ?>
                <input 
                    type="<?php echo e($field['type']); ?>" 
                    id="<?php echo e($name); ?>" 
                    name="<?php echo e($name); ?>" 
                    value="1" 
                />
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
    
    $('#description').summernote({
        height: 200
    });
</script><?php /**PATH C:\xampp\htdocs\solid-dis-direct\backend\resources\views/admin/brands/create_content.blade.php ENDPATH**/ ?>