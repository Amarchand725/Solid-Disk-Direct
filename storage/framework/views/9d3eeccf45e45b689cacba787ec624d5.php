<button
    data-toggle="tooltip" data-placement="top" title="Edit <?php echo e($singularLabel); ?>"
    data-edit-url="<?php echo e(route($routeInitialize.'.edit', $model->id)); ?>"
    data-url="<?php echo e(route($routeInitialize.'.update', $model->id)); ?>"
    class="btn btn-primary btn-sm mb-3 mb-md-0 mx-3 edit-btn"
    tabindex="0" aria-controls="DataTables_Table_0"
    type="button" data-bs-toggle="modal"
    data-bs-target="#create-pop-up-modal">
    <i class="fa fa-edit"></i>
</button>

<a href="javascript:;"
    data-toggle="tooltip" data-placement="top" title="Delete <?php echo e($singularLabel); ?>"
    class="btn btn-danger btn-sm delete"
    data-del-url="<?php echo e(route($routeInitialize.'.destroy', $model->id)); ?>">
    <i class="fa fa-trash"></i>
</a>

<?php /**PATH C:\xampp\htdocs\solid-dis-direct\backend\resources\views/admin/permissions/action.blade.php ENDPATH**/ ?>