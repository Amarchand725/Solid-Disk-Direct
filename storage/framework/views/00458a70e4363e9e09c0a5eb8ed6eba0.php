<?php echo method_field('PUT'); ?>
<div class="col-12 mb-3">
    <label class="form-label" for="exist_permission_group">Exist Permission Group</label>
    <select class="form-select" name="exist_permission_group" id="exist_permission_group">
        <option value="" selected>Select permission group</option>
        <?php $__currentLoopData = $models; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $model): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($model->id != $group_permission->id): ?>
                <option value="<?php echo e($model->label); ?>"><?php echo e(ucwords($model->label)); ?></option>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <span id="exist_permission_group_error" class="text-danger error"></span>
</div>
<div class="col-12 mb-3">
    <label class="form-label" for="new_permission_group">Permission Group</label>
    <input type="text" id="new_permission_group" value="<?php echo e($group_permission->label); ?>" name="new_permission_group" class="form-control" placeholder="Enter new permission group" autofocus />
    <span id="new_permission_group_error" class="text-danger error"></span>
</div>
<?php if(count($otherPermissions) > 0): ?>
    <?php $__currentLoopData = $otherPermissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $otherPermission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-12 mb-3">
            <label class="form-label" for="custom_permission">Custom Permission </label>
            <input type="text" id="custom_permission" name="custom_permissions[]" value="<?php echo e($otherPermission); ?>" class="form-control" placeholder="Enter custom permision name" autofocus />
            <span id="custom_permission_error" class="text-danger error"></span>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php else: ?>
    <div class="col-12 mb-3">
        <label class="form-label" for="custom_permission">Custom Permission </label>
        <input type="text" id="custom_permission" name="custom_permissions[]" class="form-control" placeholder="Enter custom permision name" autofocus />
        <span id="custom_permission_error" class="text-danger error"></span>
    </div>
<?php endif; ?>
<div class="col-12 mb-2">
    <div class="card-body border-top p-9">
        <label class="form-label" for="permissions">Check Permissions </label>
        <div class="col-lg-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="all" id="checkAll"/>
                <label class="form-check-label" for="checkAll"> <strong>All</strong> </label>
            </div>
        </div>

        <?php $__currentLoopData = subPermissionFields(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub_permission_field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-lg-3 mt-2">
                <div class="form-check">
                    <?php if(in_array($sub_permission_field, $permissions)): ?>
                        <input class="form-check-input" name="permissions[]" type="checkbox" value="<?php echo e($sub_permission_field); ?>" id="<?php echo e($sub_permission_field); ?>" checked/>
                    <?php else: ?>
                        <input class="form-check-input" name="permissions[]" type="checkbox" value="<?php echo e($sub_permission_field); ?>" id="<?php echo e($sub_permission_field); ?>"/>
                    <?php endif; ?>
                    <label class="form-check-label" for="<?php echo e($sub_permission_field); ?>"> <strong><?php echo e(Str::ucfirst($sub_permission_field)); ?></strong></label>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <span id="permissions_error" class="text-danger error"></span>
    </div>
</div>

<script>
    $("#checkAll").click(function () {
		$('input:checkbox').not(this).prop('checked', this.checked);
	});

    $('select').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),
        });
    });
</script>
<?php /**PATH C:\xampp\htdocs\Solid-Disk-Direct\Solid-Disk-Direct\resources\views/admin/permissions/edit_content.blade.php ENDPATH**/ ?>