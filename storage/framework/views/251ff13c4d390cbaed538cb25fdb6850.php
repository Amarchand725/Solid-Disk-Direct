<?php if(count($menusWithoutPermissions) > 0): ?>
    <div class="col-12 mb-3">
        <label class="form-label" for="new_permissions">
            <strong><u>New Permission Group(s):</u></strong>
        </label>
        <br />
        <span id="" class="text-danger">
            <?php $__currentLoopData = $menusWithoutPermissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menusWithoutPermission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo e(ucfirst(Str::kebab(Str::plural($menusWithoutPermission)))); ?>,
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </span>
        <hr>
    </div>
<?php endif; ?>
<div class="col-12 mb-3">
    <label class="form-label" for="exist_permission_group">Exist Permission Group</label>
    <select class="form-select" name="exist_permission_group" id="exist_permission_group">
        <option value="" selected>Select permission group</option>
        <?php $__currentLoopData = $models; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $model): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($model->label); ?>"><?php echo e(ucwords($model->label)); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <span id="exist_permission_group_error" class="text-danger error"></span>
</div>
<div class="col-12 mb-3">
    <label class="form-label" for="new_permission_group">New Permission Group</label>
    <input type="text" id="new_permission_group" name="new_permission_group" class="form-control" placeholder="Enter new permission group" autofocus />
    <span id="new_permission_group_error" class="text-danger error"></span>
</div>

<div class="col-12 mb-3">
    <label class="form-label" for="custom_permission">Custom Permission </label>
    <input type="text" id="custom_permission" name="custom_permission" class="form-control" placeholder="Enter custom permision name" autofocus />
    <span id="custom_permission_error" class="text-danger error"></span>
</div>
<div class="col-12 mb-2">
    <div class="card-body border-top p-9">
        <label class="form-label" for="permissions">Check Permissions </label>
        <!-- Default checkbox -->
        <div class="col-lg-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="all" id="checkAll"/>
                <label class="form-check-label" for="checkAll"> <strong>All</strong> </label>
            </div>
        </div>
        <?php $__currentLoopData = subPermissionFields(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$sub_permission_field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-lg-3 mt-2">
                <div class="form-check">
                    <input class="form-check-input" name="permissions[]" type="checkbox" value="<?php echo e($key); ?>" id="<?php echo e($key); ?>"/>
                    <label class="form-check-label" for="<?php echo e($key); ?>"> <strong><?php echo e($sub_permission_field); ?></strong></label>
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
</script><?php /**PATH C:\xampp\htdocs\solid-dis-direct\backend\resources\views/admin/permissions/create_content.blade.php ENDPATH**/ ?>