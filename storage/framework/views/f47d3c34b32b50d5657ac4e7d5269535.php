<div class="row">
    <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if($name != 'created_at'): ?>
            <?php if($name=='brand' || $name=='category' || $name=='thumbnail' || isset($field['type']) && $field['type'] === 'textarea'): ?>
                <div class="col-12 mb-3">
            <?php else: ?>
                <div class="col-6 mb-3">
            <?php endif; ?>
                <label class="form-label" for="<?php echo e($name); ?>">
                    <?php echo e($field['label'] ?? ucfirst($name)); ?>

                    <?php if(isset($field['required']) && $field['required']): ?> 
                        <span class="text-danger">*</span>  <!-- Display * if required -->
                    <?php endif; ?>
                </label>

                <?php if(isset($field['type']) && $field['type'] === 'select'): ?>
                    <?php if($name=='brand' && isset($brands) && !empty($brands)): ?>
                        <select id="<?php echo e($name); ?>" name="<?php echo e($name); ?>" class="form-control">
                            <option value="" selected>Select <?php echo e($name); ?></option>
                            <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> 
                                <option value="<?php echo e($brand->id); ?>" <?php echo e(old($name, $field['value']) == $brand->id ? 'selected' : ''); ?>>
                                    <?php echo e($brand->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    <?php elseif($name=='category' && isset($parent_categories) && !empty($parent_categories)): ?>
                        <select id="<?php echo e($name); ?>" name="categories[]" data-url="<?php echo e(route('categories.sub-categories')); ?>" data-category-level="parent" data-level="0" class="form-control category category-select">
                            <option value="" selected>Select parent category</option>
                            <?php $__currentLoopData = $parent_categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $parent_category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($parent_category->id); ?>">
                                    <?php echo e($parent_category->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <span id="category-container"></span>
                    <?php elseif($name=='unit' && isset($units) && !empty($units)): ?>
                        <select id="<?php echo e($name); ?>" name="<?php echo e($name); ?>" class="form-control">
                            <option value="" selected>Select <?php echo e($name); ?></option>
                            <?php $__currentLoopData = $units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> 
                                <option value="<?php echo e($unit->id); ?>" <?php echo e(old($name, $field['value']) == $unit->id ? 'selected' : ''); ?>>
                                    <?php echo e($unit->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    <?php elseif($name=='tax_type' && isset($tax_types) && !empty($tax_types)): ?>
                        <select id="<?php echo e($name); ?>" name="<?php echo e($name); ?>" class="form-control">
                            <option value="" selected>Select <?php echo e($name); ?></option>
                            <?php $__currentLoopData = $tax_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tax_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> 
                                <option value="<?php echo e($tax_type->id); ?>" <?php echo e(old($name, $field['value']) == $tax_type->id ? 'selected' : ''); ?>>
                                    <?php echo e($tax_type->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    <?php elseif($name=='condition' && isset($product_conditions) && !empty($product_conditions)): ?>
                        <select id="<?php echo e($name); ?>" name="<?php echo e($name); ?>" class="form-control">
                            <option value="" selected>Select <?php echo e($name); ?></option>
                            <?php $__currentLoopData = $product_conditions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $condition): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> 
                                <option value="<?php echo e($condition->id); ?>" <?php echo e(old($name, $field['value']) == $condition->id ? 'selected' : ''); ?>>
                                    <?php echo e($condition->name); ?>

                                </option>
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
                    <?php if($name=='unit_price'): ?>
                        <input 
                            type="<?php echo e($field['type'] ?? 'text'); ?>" 
                            id="<?php echo e($name); ?>" 
                            name="<?php echo e($name); ?>" 
                            class="form-control numeric" 
                            placeholder="<?php echo e($field['placeholder'] ?? ''); ?>" 
                            value="<?php echo e(old($name, $field['value'] ?? '')); ?>" 
                            autofocus
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
    

    <div class="col-12 mb-3">
        <label class="form-label" for="images">
            Additional Images
        </label>
        <input type="file" accept="image/*" class="form-control" multiple name="images[]" id="images">
        <div id="image-preview" style="margin-top: 1rem; display: flex; gap: 10px; flex-wrap: wrap;"></div>
        <span id="images_error" class="text-danger error"></span>
    </div>
</div>
<script src="<?php echo e(asset('admin')); ?>/custom/product.js"></script> 
<script>
    $('#short_description').summernote({
        height: 200
    });
    
    $('#full_description').summernote({
        height: 200
    });

    $('select').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),
        });
    });

    setupImagePreview('images', 'image-preview');

    // document.getElementById('images').addEventListener('change', function(event) {
    //     const preview = document.getElementById('image-preview');
    //     preview.innerHTML = ''; // Clear previous

    //     const files = Array.from(event.target.files);

    //     files.forEach((file, index) => {
    //         if (!file.type.startsWith('image/')) return;

    //         const reader = new FileReader();
    //         reader.onload = function(e) {
    //             const wrapper = document.createElement('div');
    //             wrapper.style.position = 'relative';
    //             wrapper.style.width = '80px';
    //             wrapper.style.height = '80px';

    //             const img = document.createElement('img');
    //             img.src = e.target.result;
    //             img.style.width = '100%';
    //             img.style.height = '100%';
    //             img.style.objectFit = 'cover';
    //             img.style.borderRadius = '6px';
    //             img.style.boxShadow = '0 2px 6px rgba(0,0,0,0.2)';

    //             const removeBtn = document.createElement('span');
    //             removeBtn.innerHTML = '&times;';
    //             removeBtn.style.position = 'absolute';
    //             removeBtn.style.top = '-5px';
    //             removeBtn.style.right = '-5px';
    //             removeBtn.style.cursor = 'pointer';
    //             removeBtn.style.background = 'red';
    //             removeBtn.style.color = 'white';
    //             removeBtn.style.borderRadius = '50%';
    //             removeBtn.style.width = '20px';
    //             removeBtn.style.height = '20px';
    //             removeBtn.style.display = 'flex';
    //             removeBtn.style.alignItems = 'center';
    //             removeBtn.style.justifyContent = 'center';
    //             removeBtn.style.fontSize = '14px';

    //             removeBtn.onclick = function() {
    //                 wrapper.remove();
    //                 // Optional: remove file from input (see below)
    //             };

    //             wrapper.appendChild(img);
    //             wrapper.appendChild(removeBtn);
    //             preview.appendChild(wrapper);
    //         };
    //         reader.readAsDataURL(file);
    //     });
    // });
</script><?php /**PATH C:\xampp\htdocs\Solid-Disk-Direct\Solid-Disk-Direct\resources\views/admin/products/create_content.blade.php ENDPATH**/ ?>