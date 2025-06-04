<div class="d-flex align-items-center">
    <a href="javascript:;" class="text-body dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
        <i class="ti ti-dots-vertical ti-sm mx-1"></i>
    </a>
    <div class="dropdown-menu dropdown-menu-end m-0">
        
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check($routeInitialize.'-show')): ?>
            <a href="<?php echo e(route($routeInitialize.'.show', $model->id)); ?>" 
               class="dropdown-item" target="_blank">
                <i class="ti ti-eye me-1"></i> View Details
            </a>
        <?php endif; ?>

        
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check($routeInitialize.'-status')): ?>
            <a href="javascript:;" 
               class="dropdown-item change-status-btn" 
               data-status-url="<?php echo e(route($routeInitialize.'.changeStatus', $model->id)); ?>"
               data-current-status="<?php echo e($model->order_status); ?>"
               data-bs-toggle="modal" 
               data-bs-target="#change-status-modal">
                <i class="ti ti-arrows-exchange me-1"></i> Change Status
            </a>
        <?php endif; ?>

        
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check($routeInitialize.'-invoice')): ?>
            <a href="<?php echo e(route($routeInitialize.'.invoice', $model->id)); ?>" 
               class="dropdown-item" 
               target="_blank">
                <i class="ti ti-printer me-1"></i> Print Invoice
            </a>
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
<?php /**PATH C:\xampp\htdocs\solid-dis-direct\backend\resources\views/admin/orders/action.blade.php ENDPATH**/ ?>