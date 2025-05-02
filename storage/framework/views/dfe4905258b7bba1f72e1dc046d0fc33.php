<?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php if($name != 'created_at'): ?>
        <div class="col-12 mb-3">
            <label class="form-label" for="<?php echo e($name); ?>">
                <?php echo e($field['label'] ?? ucfirst($name)); ?>

                <?php if(isset($field['required']) && $field['required']): ?> 
                    <span class="text-danger">*</span>  <!-- Display * if required -->
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
                            <option value="<?php echo e($menuGroup->id); ?>"><?php echo e($menuGroup->menu); ?></option>
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
            <?php elseif(isset($field['type']) && $field['type'] === 'textarea'): ?>
                <textarea id="<?php echo e($name); ?>" name="<?php echo e($name); ?>" class="form-control" placeholder="<?php echo e($field['placeholder'] ?? ''); ?>"><?php echo e(old($name, $field['value'] ?? '')); ?></textarea>
            <?php elseif(isset($field['type']) && $field['type'] === 'file'): ?>
                <input 
                    type="<?php echo e($field['type'] ?? 'text'); ?>" 
                    id="file-uploader" 
                    name="<?php echo e($name); ?>" 
                    accept="<?php echo e(isset($field['accept']) ? $field['accept'] : ''); ?>" 
                    class="form-control" 
                    placeholder="<?php echo e($field['placeholder'] ?? ''); ?>" 
                    value="<?php echo e(old($name, $field['value'] ?? '')); ?>" 
                    autofocus
                />

                <span id="preview">
                    <?php if(!empty($field['value'])): ?>
                        <img src="<?php echo e(asset('storage/' . $field['value'])); ?>" style="width:60px; height:50px" alt="Avatar" class="img-avatar zoomable">
                    <?php endif; ?>
                </span>
            <?php elseif(isset($name) && $name === 'fields'): ?>
                <div class="row">
                    <div class="col-sm-3">
                        <select name="types[]" id="type" class="form-control">
                            <option value="" selected>Choose data type</option>
                            <?php $__currentLoopData = fieldTypes(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$fieldType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>" ><?php echo e($fieldType); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <select name="input_types[]" id="input_type" class="form-control">
                            <option value="" selected>Choose input type</option>
                            <?php $__currentLoopData = inputTypes(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inputKey=>$inputType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($inputKey); ?>" ><?php echo e($inputType); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-sm-5">
                        <input 
                            type="<?php echo e($field['type'] ?? 'text'); ?>" 
                            id="<?php echo e($name); ?>" 
                            name="<?php echo e($name); ?>[]" 
                            class="form-control" 
                            placeholder="<?php echo e($field['placeholder'] ?? ''); ?>" 
                            value="" 
                            autofocus
                        />
                    </div>
                    <div class="col-sm-1">
                        <button type="button" class="btn btn-success" id="add-more-btn">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
                <span id="add-more-content"></span>
            <?php else: ?>
                <?php if($name=='icon'): ?>
                    <input 
                        type="<?php echo e($field['type'] ?? 'text'); ?>" 
                        id="<?php echo e($name); ?>" 
                        name="<?php echo e($name); ?>" 
                        class="form-control" 
                        placeholder="<?php echo e($field['placeholder'] ?? ''); ?>" 
                        value="<?php echo e(old($name, $field['value'] ?? '')); ?>" 
                        autofocus
                    />
                    <div class="input-group-append">
                        <a href="https://tabler-icons.io/" target="_blank" class="btn btn-success mt-2">Browse Icons</a>
                    </div>
                    <small class="form-text text-muted">
                        Go to <a href="https://tabler-icons.io/" target="_blank">tabler-icons.io</a>, copy the icon name like <code>ti ti-smart-home</code>, and paste it here.
                    </small>
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
</script><?php /**PATH C:\xampp\htdocs\Solid-Disk-Direct\Solid-Disk-Direct\resources\views/admin/menus/create_content.blade.php ENDPATH**/ ?>