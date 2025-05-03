<?php $__env->startSection('title',  ($exception->getStatusCode() ?? 'Unknown'). '-'. 'Page Not Found'); ?>
<?php $__env->startSection('content'); ?>
  <div class="container-xxl container-p-y d-flex flex-column align-items-center justify-content-center min-vh-100 text-center">
    <div class="misc-wrapper">
      <h2 class="mb-1 mt-4">Page Not Found :(</h2>
      <p class="mb-4 mx-2"><?php echo e($exception->getMessage() ?: 'Oops! 😖 Something went wrong.'); ?></p>
      <a href="<?php echo e(route('admin.login')); ?>" class="btn btn-primary mb-4">Back to home</a>
      <div class="mt-4">
        <img src="<?php echo e(asset('admin')); ?>/assets/img/illustrations/page-misc-error.png"
          alt="page-misc-error"
          width="225"
          class="img-fluid"
        />
      </div>
    </div>
  </div>
  <div class="container-fluid misc-bg-wrapper position-fixed w-100 h-100 d-flex align-items-center justify-content-center">
    <img
      src="<?php echo e(asset('admin')); ?>/assets/img/illustrations/bg-shape-image-light.png"
      alt="page-misc-error"
      data-app-light-img="illustrations/bg-shape-image-light.png"
      data-app-dark-img="illustrations/bg-shape-image-dark.png"
    />
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.auth.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Solid-Disk-Direct\Solid-Disk-Direct\resources\views/errors/404.blade.php ENDPATH**/ ?>