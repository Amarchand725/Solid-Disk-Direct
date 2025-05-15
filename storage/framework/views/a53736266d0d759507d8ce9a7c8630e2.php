<?php $__env->startSection('title', $title.' -  ' . appName()); ?>
<?php $__env->startPush('css'); ?>
    <style>
        .bs-stepper.vertical .bs-stepper-header {
            max-height: 530px; /* Adjust height as needed */
            overflow-y: auto;
            overflow-x: hidden;
            padding-right: 5px; /* Space for scrollbar */
            scrollbar-width: thin; /* Firefox */
        }

        .bs-stepper.vertical .bs-stepper-header::-webkit-scrollbar {
            width: 6px;
        }

        .bs-stepper.vertical .bs-stepper-header::-webkit-scrollbar-thumb {
            background-color: #ccc;
            border-radius: 4px;
        }
    </style>
<?php $__env->stopPush(); ?>
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
                                data-toggle="tooltip" data-placement="top" title="Add <?php echo e($singularLabel); ?>"
                                data-url="<?php echo e(route($routeInitialize.'.store')); ?>"
                                data-create-url="<?php echo e(route($routeInitialize.'.create')); ?>"
                                class="btn btn-primary add-btn mb-3 mb-md-0 mx-3"
                                tabindex="0" aria-controls="DataTables_Table_0"
                                type="button" data-bs-toggle="modal"
                                data-bs-target="#create-pop-up-modal">
                                <span>
                                    <i class="ti ti-plus me-0 me-sm-1 ti-xs"></i>
                                    <span class="d-none d-sm-inline-block"> Add <?php echo e($singularLabel); ?> </span>
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
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Solid-Disk-Direct\Solid-Disk-Direct\resources\views/admin/menus/index.blade.php ENDPATH**/ ?>