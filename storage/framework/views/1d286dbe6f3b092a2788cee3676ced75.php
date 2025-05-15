<?php echo method_field('PUT'); ?>
<input type="hidden" name="pre_menu" value="<?php echo e($menu->menu); ?>">
<div class="row">
    <!-- Vertical Icons Wizard -->
    <div class="col-12 mb-4">
      <small class="text-light fw-semibold">Menu Fields</small>
      <div class="bs-stepper vertical wizard-vertical-icons-example mt-2">
        <div class="bs-stepper-header">
            <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="step" data-target="#<?php echo e($field['name']); ?>">
                    <button type="button" class="step-trigger">
                        <span class="bs-stepper-circle">
                            <i class="ti ti-file-description"></i>
                        </span>
                        <span class="bs-stepper-label">
                            <span class="bs-stepper-title"><?php echo e($field['label'] ?? ''); ?></span>
                            <span class="bs-stepper-subtitle">Setup <?php echo e($field['label'] ?? ''); ?> </span>
                        </span>
                    </button>
                </div>
                <div class="line"></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="bs-stepper-content">
            <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div id="<?php echo e($field['name']); ?>" class="content">
                    <div class="content-header mb-3">
                        <h6 class="mb-0"><?php echo e($field['label'] ?? ucfirst($name)); ?></h6>
                        <small>Enter <?php echo e($field['label'] ?? $name); ?> Settings.</small>
                    </div>
                    <div class="row g-3">
                        <div class="col-sm-12">
                            
                            <div class="mb-3">
                                <label>Data Type</label>
                                <select name="fields[<?php echo e($field['name']); ?>][type]" class="form-select w-full">
                                    <?php $__currentLoopData = fieldTypes(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$fieldType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($key); ?>" <?php echo e($field['type']==$key ? 'selected' : ''); ?>><?php echo e($fieldType); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    
                                </select>
                            </div>

                            
                            <div class="mb-3">
                                <label>Input Type</label>
                                <select name="fields[<?php echo e($field['name']); ?>][input_type]" class="form-select w-full">
                                    <?php $__currentLoopData = inputTypes(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$inputType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($key); ?>" <?php echo e($field['input_type']==$key ? 'selected' : ''); ?>><?php echo e($inputType); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    
                                </select>
                            </div>

                            
                            <div class="mb-3">
                                <label>Label</label>
                                <input type="text" name="fields[<?php echo e($field['name']); ?>][label]" value="<?php echo e($field['label']); ?>" class="form-control w-full">
                            </div>

                            
                            <div class="mb-3">
                                <label>Placeholder</label>
                                <input type="text" name="fields[<?php echo e($field['name']); ?>][placeholder]" value="<?php echo e($field['placeholder']); ?>" class="form-control w-full">
                            </div>

                            
                            <?php $__currentLoopData = ['required', 'index_visible', 'create_visible', 'edit_visible', 'show_visible']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="mb-2">
                                    <label>
                                        <input type="checkbox" name="fields[<?php echo e($field['name']); ?>][<?php echo e($attr); ?>]" value="1" <?php echo e(!empty($field[$attr]) ? 'checked' : ''); ?>>
                                        <?php echo e(ucfirst(str_replace('_', ' ', $attr))); ?>

                                    </label>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            
                            <div class="mb-3">
                                <label>Extra (JSON)</label>
                                <textarea name="fields[<?php echo e($field['name']); ?>][extra]" placeholder="{'validation':'max:255'}" class="form-control w-full" rows="3"><?php echo e($field['extra']); ?></textarea>
                            </div>
                        </div>
                        
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>
    </div>
    <!-- /Vertical Icons Wizard -->
</div>

<script>
    $('select').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),
        });
    });

    window.stepper = new Stepper(document.querySelector('.wizard-vertical-icons-example'), {
        linear: false,
        animation: true
    });

    // Bind step navigation buttons
    document.querySelectorAll('.btn-next').forEach(button => {
        button.addEventListener('click', function () {
            window.stepper.next();
        });
    });

    document.querySelectorAll('.btn-prev').forEach(button => {
        button.addEventListener('click', function () {
            window.stepper.previous();
        });
    });
</script><?php /**PATH C:\xampp\htdocs\Solid-Disk-Direct\Solid-Disk-Direct\resources\views/admin/menu_fields/edit_content.blade.php ENDPATH**/ ?>