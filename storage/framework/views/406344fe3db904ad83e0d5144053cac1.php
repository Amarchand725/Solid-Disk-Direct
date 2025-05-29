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
                <?php if($name=='parent'): ?>
                    <select id="category" name="categories[]" 
                        data-url="<?php echo e(route('categories.sub-categories')); ?>" 
                        data-category-level="parent" data-level="0" 
                        class="form-control category category-select"
                    >
                        <option value="" selected>Select <?php echo e($name); ?></option>
                        <?php $__currentLoopData = $parent_categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($category->id); ?>"
                                <?php echo e(collect(old($name, $model->parents->pluck('id')->toArray()))->contains($category->id) ? 'selected' : ''); ?>>
                                <?php echo e($category->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <span id="category-container">
                        <?php $counter = 0 ?> 
                        <?php $__currentLoopData = $categoriesData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $parentCategories): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $counter++ ?> 
                            <div class="col-12 mt-3 sub-category-container">
                                <label class="form-label" for="sub-category-<?php echo e($counter); ?>">Sub Category</label>
                                <select 
                                    id="sub-category-<?php echo e($counter); ?>" 
                                    name="categories[]" 
                                    class="category-select form-control category" 
                                    data-level="<?php echo e($counter); ?>" 
                                    data-url="<?php echo e(route('categories.sub-categories')); ?>" 
                                    data-category-level="sub-category"
                                >
                                    <option value="" selected>Select <?php echo e($name); ?></option>
                                    <?php $__currentLoopData = $parentCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($category->id); ?>"
                                            <?php echo e(collect(old($name, $model->parents->pluck('id')->toArray()))->contains($category->id) ? 'selected' : ''); ?>>
                                            <?php echo e($category->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </span>
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
                <textarea id="<?php echo e($name); ?>" name="<?php echo e($name); ?>" class="form-control summernote" placeholder="<?php echo e($field['placeholder'] ?? ''); ?>"><?php echo old($name, $field['value'] ?? ''); ?></textarea>
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
            <?php elseif(isset($field['type']) && $field['type'] === 'checkbox'): ?>
                <input 
                    type="<?php echo e($field['type']); ?>" 
                    id="<?php echo e($name); ?>" 
                    name="<?php echo e($name); ?>" 
                    value="1" <?php if($field['value']==1): ?> checked <?php endif; ?> 
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

<script src="<?php echo e(asset('admin')); ?>/custom/multi-categories.js"></script> 
<script>
    $('select').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),
        });
    });
    $('#description').summernote({
        height: 200
    });
</script><?php /**PATH C:\xampp\htdocs\Solid-Disk-Direct\Solid-Disk-Direct\resources\views/admin/categories/edit_content.blade.php ENDPATH**/ ?>