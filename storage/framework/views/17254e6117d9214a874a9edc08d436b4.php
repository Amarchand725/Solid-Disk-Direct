<?php echo method_field('PUT'); ?>
<input type="hidden" name="pre_menu" value="<?php echo e($model->menu); ?>">
<?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php if($name != 'created_at' && $name != 'fields'): ?>
        <div class="col-12 mb-3">
            <label class="form-label" for="<?php echo e($name); ?>">
                <?php echo e($field['label'] ?? ucfirst($name)); ?>

                <?php if(isset($field['required']) && $field['required']): ?>
                    <span class="text-danger">*</span>  <!-- Display * if required and not file type -->
                <?php endif; ?>
            </label>

            <?php if(isset($field['type']) && $field['type'] === 'select' || $name=='menu_group' || $name=='icon'): ?>
                <?php if($name=='icon'): ?>
                    <select id="<?php echo e($name); ?>" name="<?php echo e($name); ?>" class="form-control">
                        <option value="" selected>Select Icon</option>
                        <?php $__currentLoopData = getTabIcons(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tabIcon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($tabIcon); ?>" <?php echo e($model->icon==$tabIcon?'selected':''); ?>>
                                <i class="<?php echo $tabIcon; ?>"></i> <?php echo e($tabIcon); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <div id="icon-preview" style="margin-top: 10px; font-size: 24px;">
                        <?php if($model->icon): ?>
                            <i class="<?php echo e($model->icon); ?>"></i>
                        <?php endif; ?>
                    </div>
                <?php elseif($name=='menu_group'): ?>
                    <select id="<?php echo e($name); ?>" name="<?php echo e($name); ?>" class="form-control">
                        <option value="" selected>Select Menu Group</option>
                        <?php $__currentLoopData = $menuGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menuGroup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($menuGroup->id); ?>" <?php echo e($model->menu_group==$menuGroup->id?'selected':''); ?>><?php echo e($menuGroup->menu); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                <?php else: ?>
                    <select id="<?php echo e($name); ?>" name="<?php echo e($name); ?>" class="form-control">
                        <?php $__currentLoopData = $field['options'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>  <!-- Safely handle 'options' -->
                            <option value="<?php echo e($key); ?>" <?php echo e(old($name, $field['value']) == $key ? 'selected' : ''); ?>>
                                <?php echo e($option); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                <?php endif; ?>
            <?php else: ?>
                <?php if($name=='menu'): ?>
                    <input 
                        type="<?php echo e($field['type'] ?? 'text'); ?>" 
                        class="form-control" 
                        placeholder="<?php echo e($field['placeholder'] ?? ''); ?>" 
                        value="<?php echo e(old($name, $field['value'] ?? '')); ?>" 
                        disabled
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
    $('#add-more-btn').on('click', function(){
        var html = '';
        html = '<div class="row mt-2">'+
                    '<div class="col-sm-3">'+
                        '<select name="types[]" id="type" class="form-control">'+
                            '<option value="" selected>Choose type</option>';
                            <?php $__currentLoopData = fieldTypes(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$fieldType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                html += '<option value="<?php echo e($key); ?>" ><?php echo e($fieldType); ?></option>';
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        html += '</select>'+
                    '</div>'+
                    '<div class="col-sm-3">'+
                        '<select name="input_types[]" id="input_type" class="form-control">'+
                            '<option value="" selected>Choose input type</option>';
                            <?php $__currentLoopData = inputTypes(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inputKey=>$inputType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                html += '<option value="<?php echo e($inputKey); ?>" ><?php echo e($inputType); ?></option>';
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        html += '</select>'+
                    '</div>'+
                    '<div class="col-sm-5">'+
                        '<input '+
                            'type="text" '+
                            'id="fields" '+
                            'name="fields[]" '+
                            'class="form-control" '+
                            'placeholder="Enter field name" '+
                        '/>'+
                    '</div>'+
                    '<div class="col-sm-1">'+
                        '<button type="button" class="btn btn-danger remove-btn" id="remove-btn">'+
                            '<i class="fa fa-times"></i>'+
                        '</button>'+
                    '</div>'+
                '</div>';
        $('#add-more-content').append(html);
    })

    // Use event delegation for dynamically added remove buttons
    $(document).on('click', '.remove-btn', function(){
        $(this).closest('.row').remove();
    });
</script>
<script>
    $(document).ready(function () {
        // If using Select2:
        $('#icon').on('change', function () {
            const selectedIcon = $(this).val();
            $('#icon-preview').html(`<i class="${selectedIcon}"></i>`);
        });
    });
</script><?php /**PATH C:\xampp\htdocs\solid-dis-direct\backend\resources\views/admin/menus/edit_content.blade.php ENDPATH**/ ?>