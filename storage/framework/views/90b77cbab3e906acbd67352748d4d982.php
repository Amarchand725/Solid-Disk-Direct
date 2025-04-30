<!DOCTYPE html>

<html
  lang="en"
  class="light-style customizer-hide"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="<?php echo e(asset('admin/assets/')); ?>"
  data-template="vertical-menu-template-no-customizer"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title><?php echo $__env->yieldContent('title'); ?></title>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <meta name="description" content="" />

    <!-- Favicon -->
    <?php if(!empty(settings()->favicon)): ?>
        <link rel="icon" type="image/x-icon" href="<?php echo e(asset('storage').'/'.settings()->favicon); ?>" />
    <?php else: ?>
        <link rel="icon" type="image/x-icon" href="<?php echo e(asset('admin/assets/img/favicon/favicon.ico')); ?>" />
    <?php endif; ?>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- Icons -->
    <link rel="stylesheet" href="<?php echo e(asset('admin')); ?>/assets/vendor/fonts/fontawesome.css" />
    <link rel="stylesheet" href="<?php echo e(asset('admin')); ?>/assets/vendor/fonts/tabler-icons.css" />
    <link rel="stylesheet" href="<?php echo e(asset('admin')); ?>/assets/vendor/fonts/flag-icons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('admin')); ?>/assets/vendor/css/rtl/core.css" />
    <link rel="stylesheet" href="<?php echo e(asset('admin')); ?>/assets/vendor/css/rtl/theme-default.css" />
    <link rel="stylesheet" href="<?php echo e(asset('admin')); ?>/assets/css/demo.css" />
    <link rel="stylesheet" href="<?php echo e(asset('admin')); ?>/assets/css/custom.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('admin')); ?>/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="<?php echo e(asset('admin')); ?>/assets/vendor/libs/node-waves/node-waves.css" />
    <link rel="stylesheet" href="<?php echo e(asset('admin')); ?>/assets/vendor/libs/typeahead-js/typeahead.css" />
    <link rel="stylesheet" href="<?php echo e(asset('admin')); ?>/assets/vendor/libs/bs-stepper/bs-stepper.css" />
    <link rel="stylesheet" href="<?php echo e(asset('admin')); ?>/assets/vendor/libs/apex-charts/apex-charts.css" />
    <link rel="stylesheet" href="<?php echo e(asset('admin')); ?>/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('admin')); ?>/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('admin')); ?>/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('admin/assets/vendor/libs/select2/select2.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('admin/assets/css/toastr.min.css')); ?>">

    <link rel="stylesheet" href="<?php echo e(asset('admin/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('admin/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css')); ?>" />

    <link rel="stylesheet" href="<?php echo e(asset('admin/assets/datatables/jquery.dataTables.min.css')); ?>" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
    <!-- Helpers -->
    <script src="<?php echo e(asset('admin')); ?>/assets/vendor/js/helpers.js"></script>

    <script src="<?php echo e(asset('admin')); ?>/assets/js/config.js"></script>


    <?php echo $__env->yieldPushContent('css'); ?>

  </head>

  <body>
    <!-- Dashboard Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            <?php if (isset($component)) { $__componentOriginal5a9382bf5c9907cca433c31a03b724ce = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5a9382bf5c9907cca433c31a03b724ce = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.side-bar-menu','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('side-bar-menu'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5a9382bf5c9907cca433c31a03b724ce)): ?>
<?php $attributes = $__attributesOriginal5a9382bf5c9907cca433c31a03b724ce; ?>
<?php unset($__attributesOriginal5a9382bf5c9907cca433c31a03b724ce); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5a9382bf5c9907cca433c31a03b724ce)): ?>
<?php $component = $__componentOriginal5a9382bf5c9907cca433c31a03b724ce; ?>
<?php unset($__componentOriginal5a9382bf5c9907cca433c31a03b724ce); ?>
<?php endif; ?>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                <?php if (isset($component)) { $__componentOriginal850419188ae35167c7319eecf5d82db1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal850419188ae35167c7319eecf5d82db1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.nav-bar','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('nav-bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal850419188ae35167c7319eecf5d82db1)): ?>
<?php $attributes = $__attributesOriginal850419188ae35167c7319eecf5d82db1; ?>
<?php unset($__attributesOriginal850419188ae35167c7319eecf5d82db1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal850419188ae35167c7319eecf5d82db1)): ?>
<?php $component = $__componentOriginal850419188ae35167c7319eecf5d82db1; ?>
<?php unset($__componentOriginal850419188ae35167c7319eecf5d82db1); ?>
<?php endif; ?>
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <?php echo $__env->yieldContent('content'); ?>
                    <!-- / Content -->

                    <!-- Footer -->
                    <?php if (isset($component)) { $__componentOriginal99051027c5120c83a2f9a5ae7c4c3cfa = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal99051027c5120c83a2f9a5ae7c4c3cfa = $attributes; } ?>
<?php $component = App\View\Components\Footer::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('footer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Footer::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal99051027c5120c83a2f9a5ae7c4c3cfa)): ?>
<?php $attributes = $__attributesOriginal99051027c5120c83a2f9a5ae7c4c3cfa; ?>
<?php unset($__attributesOriginal99051027c5120c83a2f9a5ae7c4c3cfa); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal99051027c5120c83a2f9a5ae7c4c3cfa)): ?>
<?php $component = $__componentOriginal99051027c5120c83a2f9a5ae7c4c3cfa; ?>
<?php unset($__componentOriginal99051027c5120c83a2f9a5ae7c4c3cfa); ?>
<?php endif; ?>
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>

        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>
    </div>
    <!-- / Dashboard Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="<?php echo e(asset('admin')); ?>/assets/vendor/libs/jquery/jquery.js"></script>
    <script src="<?php echo e(asset('admin')); ?>/assets/vendor/libs/popper/popper.js"></script>
    <script src="<?php echo e(asset('admin')); ?>/assets/vendor/js/bootstrap.js"></script>
    <script src="<?php echo e(asset('admin')); ?>/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="<?php echo e(asset('admin')); ?>/assets/vendor/libs/node-waves/node-waves.js"></script>
    <!-- Include Select2 library -->
    <script src="<?php echo e(asset('admin/assets/js/select2.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admin')); ?>/custom/ajax-request.js"></script>
    <!-- Multi date picker to filter summary -->
    

    <script src="<?php echo e(asset('admin')); ?>/assets/vendor/libs/hammer/hammer.js"></script>
    <script src="<?php echo e(asset('admin')); ?>/assets/vendor/libs/i18n/i18n.js"></script>
    <script src="<?php echo e(asset('admin')); ?>/assets/vendor/libs/typeahead-js/typeahead.js"></script>

    <script src="<?php echo e(asset('admin')); ?>/assets/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="<?php echo e(asset('admin')); ?>/assets/vendor/libs/bs-stepper/bs-stepper.js"></script>
    <script src="<?php echo e(asset('admin')); ?>/assets/vendor/libs/apex-charts/apexcharts.js"></script>
    
    <script src="<?php echo e(asset('admin')); ?>/assets/datatables/jquery.dataTables.min.js"></script>

    <!-- Main JS -->
    <script src="<?php echo e(asset('admin')); ?>/assets/js/main.js"></script>
    

    <!-- Include Summernote -->
    <link href="<?php echo e(asset('admin')); ?>/assets/summernote/summernote.min.css" rel="stylesheet">
    <script src="<?php echo e(asset('admin')); ?>/assets/summernote/summernote.min.js"></script>

    <!-- Page JS -->
    <script src="<?php echo e(asset('admin/assets/js/toastr.min.js')); ?>"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

    <!-- Page JS -->
    <script src="<?php echo e(asset('admin')); ?>/assets/js/dashboards-ecommerce.js"></script>
    <script src="<?php echo e(asset('admin')); ?>/custom/custom.js"></script>

    <script>
        var btn = $('#scrollTop');

        $(window).scroll(function() {
          if ($(window).scrollTop() > 300) {
            btn.addClass('show');
          } else {
            btn.removeClass('show');
          }
        });

        btn.on('click', function(e) {
          e.preventDefault();
          $('html, body').animate({scrollTop:0}, '300');
        });

        function hideLoader() {
            $('#loading-gif').hide();
        }

        $(window).ready(hideLoader);
        <?php if(Session::has('message')): ?>
            toastr.options =
            {
                "closeButton" : true,
                "progressBar" : true
            }
            toastr.success("<?php echo e(session('message')); ?>");
        <?php endif; ?>

        <?php if(Session::has('error')): ?>
            toastr.options =
            {
                "closeButton" : true,
                "progressBar" : true
            }
            toastr.error("<?php echo e(session('error')); ?>");
        <?php endif; ?>

        <?php if(Session::has('info')): ?>
            toastr.options =
            {
                "closeButton" : true,
                "progressBar" : true
            }
            toastr.info("<?php echo e(session('info')); ?>");
        <?php endif; ?>

        <?php if(Session::has('warning')): ?>
            toastr.options =
            {
                "closeButton" : true,
                "progressBar" : true
            }
            toastr.warning("<?php echo e(session('warning')); ?>");
        <?php endif; ?>
    </script>

    <?php echo $__env->yieldPushContent('js'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Solid-Disk-Direct\Solid-Disk-Direct\resources\views/admin/layouts/app.blade.php ENDPATH**/ ?>