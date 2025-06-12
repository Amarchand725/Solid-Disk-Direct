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
                <i class="ti ti-eye me-1"></i> View Details
            </a>
        <?php endif; ?>

        
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check($routeInitialize.'-invoice')): ?>
            <a href="<?php echo e(route($routeInitialize.'.invoice', $model->id)); ?>" 
               class="dropdown-item" 
               target="_blank">
                <i class="ti ti-file me-1"></i> Invoice
            </a>
        <?php endif; ?>

        
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check($routeInitialize.'-status')): ?>
            <button
                data-toggle="tooltip" data-placement="top" title="Change Status <?php echo e($singularLabel); ?>"
                data-url="<?php echo e(route($routeInitialize.'.update', $model->id)); ?>"
                class="dropdown-item edit-btn change-status-btn"
                tabindex="0" aria-controls="DataTables_Table_0"
                type="button" data-bs-toggle="modal"
                data-current-status="<?php echo e($model->order_status); ?>"
                data-tracking-id="<?php echo e($model->tracking_id); ?>"
                data-bs-target="#create-pop-up-modal">
                <i class="ti ti-arrows-exchange me-1"></i> Change Status
            </button>
        <?php endif; ?>

        
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check($routeInitialize.'-delete')): ?>
            <a href="javascript:;" 
               class="dropdown-item delete" 
               data-del-url="<?php echo e(route($routeInitialize.'.destroy', $model->id)); ?>">
                <i class="ti ti-trash me-1"></i> Delete
            </a>
        <?php endif; ?> 
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\Solid-Disk-Direct\Solid-Disk-Direct\resources\views/admin/orders/action.blade.php ENDPATH**/ ?>