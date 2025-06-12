<?php $__env->startSection('title', $title.' -  ' . appName()); ?>
<?php $__env->startSection('content'); ?>
<input type="hidden" id="page_url" value="<?php echo e(route($routeInitialize.'.index')); ?>">
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-4">
            <div class="row">
                <div class="col-md-6">
                    <div class="card-header">
                        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Home /</span> <?php echo e($title); ?></h4>
                    </div>
                </div>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check($routeInitialize.'-create')): ?>
                    <div class="col-md-6">
                        <div class="dt-buttons btn-group flex-wrap float-end mt-4">
                            <button
                                id="add-btn"
                                data-toggle="tooltip" data-placement="top" 
                                title="Add <?php echo e($singularLabel); ?>"
                                data-url="<?php echo e(route($routeInitialize.'.store')); ?>"
                                data-create-url="<?php echo e(route($routeInitialize.'.create')); ?>"
                                class="btn btn-primary add-btn mb-3 mb-md-0 mx-3
                                tabindex="0" aria-controls="DataTables_Table_0"
                                type="button" data-bs-toggle="modal"
                                data-bs-target="#create-pop-up-modal">
                                <span>
                                    <i class="ti ti-plus me-0 me-sm-1 ti-xs"></i>
                                    <span class="d-none d-sm-inline-block"> 
                                        Add <?php echo e($singularLabel); ?> 
                                        <?php if(count(getNewMenus()) > 0): ?>
                                            <span class="blink-text">&#9733;</span>
                                        <?php endif; ?>
                                    </span>
                                </span>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <!-- Users List Table -->
        <div class="card">
            <div class="card-datatable table-responsive">
                <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                    <div class="container">
                        <table class="dt-row-grouping table dataTable dtr-column data_table">
                            <thead>
                                <tr>
                                    <?php $__currentLoopData = $columnsConfig; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $columnName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <th><?php echo e($columnName['name']); ?></th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                            </thead>
                            <tbody id="body"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="create-pop-up-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content p-3 p-md-5">
            <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <h3 class="mb-2" id="modal-label"></h3>
                </div>
                <form method="POST" class="pt-0 fv-plugins-bootstrap5 fv-plugins-framework" action="" id="create-form" data-modal-id="create-pop-up-modal">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <span id="edit-content">
                        <div class="mb-3">
                            <label class="form-label">Order Status</label>
                            <select name="status" id="order-status-select" class="form-select" required>
                            <?php $__currentLoopData = orderStatus(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($status); ?>"><?php echo e(ucfirst($status)); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tracking ID</label>
                            <input type="text" name="tracking_id" id="tracking-id-input" placeholder="Enter order tracking ID" class="form-control" />
                        </div>
                    </span>
                    <div class="col-12 mt-3 action-btn">
                        <div class="demo-inline-spacing sub-btn">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1 submitBtn">Submit</button>
                            <button type="reset" class="btn btn-label-secondary btn-reset" data-bs-dismiss="modal" aria-label="Close">
                                Cancel
                            </button>
                        </div>
                        <div class="demo-inline-spacing loading-btn" style="display: none;">
                            <button class="btn btn-primary waves-effect waves-light" type="button" disabled="">
                            <span class="spinner-border me-1" role="status" aria-hidden="true"></span>
                            Loading...
                            </button>
                            <button type="reset" class="btn btn-label-secondary btn-reset" data-bs-dismiss="modal" aria-label="Close">
                                Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<?php if (isset($component)) { $__componentOriginalec44ea46082c33e0f8cbcb5b200babc6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalec44ea46082c33e0f8cbcb5b200babc6 = $attributes; } ?>
<?php $component = App\View\Components\Modals::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('modals'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Modals::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalec44ea46082c33e0f8cbcb5b200babc6)): ?>
<?php $attributes = $__attributesOriginalec44ea46082c33e0f8cbcb5b200babc6; ?>
<?php unset($__attributesOriginalec44ea46082c33e0f8cbcb5b200babc6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalec44ea46082c33e0f8cbcb5b200babc6)): ?>
<?php $component = $__componentOriginalec44ea46082c33e0f8cbcb5b200babc6; ?>
<?php unset($__componentOriginalec44ea46082c33e0f8cbcb5b200babc6); ?>
<?php endif; ?>
<!--/ Modals -->
<?php $__env->stopSection(); ?>
<?php $__env->startPush('js'); ?>
<script>
    //datatable
    $(document).ready(function(){
        var page_url = $('#page_url').val();
        var columns =     <?php echo json_encode($columnsConfig); ?>  // Get columns dynamically from controller
        initializeDataTable(page_url, columns);
    })

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.change-status-btn').forEach(function (button) {
            button.addEventListener('click', function () {
                const url = this.dataset.statusUrl;
                const currentStatus = this.dataset.currentStatus;
                const trackingId = this.dataset.trackingId || '';

                // Set action and fields
                document.getElementById('change-status-form').action = url;
                document.getElementById('order-status-select').value = currentStatus;
                document.getElementById('tracking-id-input').value = trackingId;
            });
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Solid-Disk-Direct\Solid-Disk-Direct\resources\views/admin/orders/index.blade.php ENDPATH**/ ?>