<div class="d-flex align-items-center">
    <a href="javascript:;" class="text-body dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
        <i class="ti ti-dots-vertical ti-sm mx-1"></i>
    </a>
    <div class="dropdown-menu dropdown-menu-end m-0">
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check($routeInitialize.'-show')): ?>
            <a href="#"
                class="dropdown-item show"
                tabindex="0" aria-controls="DataTables_Table_0"
                type="button" data-bs-toggle="modal"
                data-bs-target="#details-modal"
                data-toggle="tooltip"
                data-placement="top"
                title="<?php echo e($singularLabel); ?> Details"
                data-show-url="<?php echo e(route($routeInitialize.'.show', $model->id)); ?>"
                >
                View Details
            </a>
        <?php endif; ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check($routeInitialize.'-edit')): ?>
            <button
                data-toggle="tooltip" data-placement="top" title="Edit <?php echo e($singularLabel); ?>"
                data-edit-url="<?php echo e(route($routeInitialize.'.edit', $model->id)); ?>"
                data-url="<?php echo e(route($routeInitialize.'.update', $model->id)); ?>"
                class="dropdown-item edit-btn"
                tabindex="0" aria-controls="DataTables_Table_0"
                type="button" data-bs-toggle="modal"
                data-bs-target="#create-pop-up-modal-for-file">
                Edit
            </button>
        <?php endif; ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check($routeInitialize.'-delete')): ?>
            <a href="javascript:;" class="dropdown-item delete" data-del-url="<?php echo e(route($routeInitialize.'.destroy', $model->id)); ?>">Delete</a>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\solid-dis-direct\backend\resources\views/admin/brands/action.blade.php ENDPATH**/ ?>