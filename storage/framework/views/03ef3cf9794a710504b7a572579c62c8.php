<div class="table-responsive">
    <h5>Assign Permissions</h5>
    <table class="table table-flush-spacing">
        <tbody>
            <tr>
                <td>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="selectAll" />
                        <label class="form-check-label" for="selectAll"> All </label>
                    </div>
                </td>
                <td class="text-nowrap fw-semibold">
                    Group
                </td>
                <td class="text-nowrap fw-semibold">
                    Permissions
                    <i class="ti ti-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Allows a full access to the system"></i>
                </td>
            </tr>
            <?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['permissions', 'role']) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['permissions', 'role']); ?>
<?php foreach (array_filter((['permissions', 'role']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

            <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="sub-permissions">
                    <td class="text-nowrap fw-semibold">
                        <div class="row">
                            <div class="form-check">
                                <!-- Add data-group attribute to relate selectGroup with its sub-permissions -->
                                <input class="form-check-input selectGroup" data-group="<?php echo e($permission->label); ?>" type="checkbox" id="selectGroup-<?php echo e($permission->label); ?>" />
                            </div>
                        </div>
                    </td>
                    <td class="text-nowrap fw-semibold">
                        <label class="form-check-label" for="selectGroup-<?php echo e($permission->label); ?>"> <?php echo e(ucfirst($permission->label)); ?></label>
                    </td>
                    <td>
                        <div class="row">
                            <?php $__currentLoopData = SubPermissions($permission->label); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub_permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $label = explode('-', $sub_permission->name) ?>
                                <div class="col-sm-3 mt-2">
                                    <div class="form-check me-3 me-lg-5">
                                        <?php if(isset($role) && $role->hasPermissionTo($sub_permission->name)): ?>
                                            <input class="form-check-input permissionCheckbox" checked data-group="<?php echo e($permission->label); ?>" name="permissions[]" value="<?php echo e($sub_permission->name); ?>" type="checkbox" id="userManagementRead-<?php echo e($sub_permission->id); ?>" />
                                        <?php else: ?>
                                            <input class="form-check-input permissionCheckbox" data-group="<?php echo e($permission->label); ?>" name="permissions[]" value="<?php echo e($sub_permission->name); ?>" type="checkbox" id="userManagementRead-<?php echo e($sub_permission->id); ?>" />
                                        <?php endif; ?>
                                        <label class="form-check-label" for="userManagementRead-<?php echo e($sub_permission->id); ?>"> <?php echo e(Str::ucfirst($label[1])); ?></label>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>
<?php /**PATH C:\xampp\htdocs\Solid-Disk-Direct\Solid-Disk-Direct\resources\views/components/sub-permissions.blade.php ENDPATH**/ ?>