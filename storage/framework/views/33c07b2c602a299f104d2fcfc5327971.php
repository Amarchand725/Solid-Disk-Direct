<?php $__env->startSection('title', Str::upper($title) .' | '.Str::upper(appName())); ?>

<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-4">
            <div class="row">
                <div class="col-md-6">
                    <div class="card-header">
                        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Home /</span> <?php echo e($title); ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <!-- Users List Table -->
        <div class="row g-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-datatable">
                        <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                            <div class="container">
                                <!-- Add role form -->
                                <form action="<?php echo e(route('roles.update', $role->id)); ?>" method="POST" class="pt-0 fv-plugins-bootstrap5 fv-plugins-framework" id="create-form">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PUT'); ?>

                                    <div class="row mt-4">
                                        <h5><?php echo e($title); ?></h5>
                                        <div class="mb-3 fv-plugins-icon-container col-12">
                                            <label class="form-label" for="name">Role <span class="text-danger">*</span></label>
                                            <input type="text" value="<?php echo e($role->name); ?>" class="form-control" id="name" placeholder="Enter role" name="name">
                                            <div class="fv-plugins-message-container invalid-feedback"></div>
                                            <span id="name_error" class="text-danger error"></span>
                                        </div>
                                        <div class="col-12 mb-3 action-btn d-flex justify-content-end">
                                            <div class="demo-inline-spacing sub-btn">
                                                <button type="submit" class="btn btn-primary me-sm-3 me-1 submitBtn">Submit</button>
                                                <a href="<?php echo e(route('roles.index')); ?>" class="btn btn-label-secondary btn-reset"> Cancel</a>
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
                                    </div>
                                    <div class="col-12">
                                        <!-- Permission table -->
                                        <?php if (isset($component)) { $__componentOriginala941724e48275bde3ba120eee2c166ee = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala941724e48275bde3ba120eee2c166ee = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sub-permissions','data' => ['permissions' => $permissions,'role' => $role]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('sub-permissions'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['permissions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($permissions),'role' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($role)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala941724e48275bde3ba120eee2c166ee)): ?>
<?php $attributes = $__attributesOriginala941724e48275bde3ba120eee2c166ee; ?>
<?php unset($__attributesOriginala941724e48275bde3ba120eee2c166ee); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala941724e48275bde3ba120eee2c166ee)): ?>
<?php $component = $__componentOriginala941724e48275bde3ba120eee2c166ee; ?>
<?php unset($__componentOriginala941724e48275bde3ba120eee2c166ee); ?>
<?php endif; ?>
                                        <!-- Permission table -->
                                    </div>

                                    <div class="col-12 mt-3 action-btn">
                                        <div class="demo-inline-spacing sub-btn">
                                            <button type="submit" class="btn btn-primary me-sm-3 me-1 submitBtn">Submit</button>
                                            <a href="<?php echo e(route('roles.index')); ?>" class="btn btn-label-secondary btn-reset"> Cancel</a>
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
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('js'); ?>
    <script src="<?php echo e(asset('admin/custom/check-permission-checkbox.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\solid-dis-direct\backend\resources\views/admin/roles/edit.blade.php ENDPATH**/ ?>