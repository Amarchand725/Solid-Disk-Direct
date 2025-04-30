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
    <link rel="stylesheet" href="<?php echo e(asset('admin/assets/vendor/fonts/fontawesome.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('admin/assets/vendor/fonts/tabler-icons.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('admin/assets/vendor/fonts/flag-icons.css')); ?>" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('admin/assets/vendor/css/rtl/core.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('admin/assets/vendor/css/rtl/theme-default.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('admin/assets/css/demo.css')); ?>" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('admin/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('admin/assets/vendor/libs/node-waves/node-waves.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('admin/assets/vendor/libs/typeahead-js/typeahead.css')); ?>" />
    <!-- Vendor -->
    <link rel="stylesheet" href="<?php echo e(asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')); ?>" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="<?php echo e(asset('admin/assets/vendor/css/pages/page-auth.css')); ?>" />
    <!-- Helpers -->

    <link rel="stylesheet" href="<?php echo e(asset('admin/assets/vendor/libs/select2/select2.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('admin/assets/css/toastr.min.css')); ?>">

    <?php echo $__env->yieldPushContent('css'); ?>

    <script src="<?php echo e(asset('admin/assets/vendor/js/helpers.js')); ?>"></script>

    <script src="<?php echo e(asset('admin/assets/js/config.js')); ?>"></script>
  </head>

  <body>
    <!-- Login -->
    <?php echo $__env->yieldContent('content'); ?>
    <!-- Login -->

    <!-- Core JS -->
    <script src="<?php echo e(asset('admin/assets/vendor/libs/jquery/jquery.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/assets/vendor/libs/popper/popper.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/assets/vendor/js/bootstrap.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/assets/vendor/libs/node-waves/node-waves.js')); ?>"></script>

    <script src="<?php echo e(asset('admin/assets/vendor/libs/hammer/hammer.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/assets/vendor/libs/i18n/i18n.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/assets/vendor/libs/typeahead-js/typeahead.js')); ?>"></script>

    <script src="<?php echo e(asset('admin/assets/vendor/js/menu.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/assets/js/select2.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/assets/js/toastr.min.js')); ?>"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="<?php echo e(asset('admin/assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')); ?>"></script>

    <!-- Main JS -->
    

    <!-- Page JS -->
    <script src="<?php echo e(asset('admin/assets/js/pages-auth.js')); ?>"></script>
    <script src="<?php echo e(asset('admin')); ?>/custom/ajax-request.js"></script>

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

		$(document).on("input", ".numeric", function() {
			this.value = this.value.replace(/\D/g,'');
		});

        $(document).ready(function() {
            $('.form-select').select2();
        });
        if (typeof description !== 'undefined') {
          CKEDITOR.replace('description');
        }

        $(document).on('keyup', '.cnic_number', function() {
            var cnic = $(this).val();
            var formattedCnic = formatCnic(cnic);
            $(this).val(formattedCnic);
        });

        function formatCnic(cnic) {
            cnic = cnic.replace(/\D/g, ''); // Remove non-numeric characters
            if (cnic.length > 5) {
                cnic = cnic.substring(0, 5) + "-" + cnic.substring(5, 12) + "-" + cnic.substring(12, 13);
            } else if (cnic.length > 2) {
                cnic = cnic.substring(0, 5) + "-" + cnic.substring(5);
            }
            return cnic;
        }
        $(document).on('keyup', '.mobileNumber', function() {
            var mobile = $(this).val();
            var formattedMobile = formatMobileNumber(mobile);
            $(this).val(formattedMobile);
        });

        function formatMobileNumber(mobile) {
            mobile = mobile.replace(/\D/g, ''); // Remove non-numeric characters
            if (mobile.length > 4) {
                mobile = mobile.substring(0, 4) + "-" + mobile.substring(4, 11);
            }
            return mobile;
        }

        $(document).on('keyup', '.phoneNumber', function() {
            var phone = $(this).val();
            var formattedPhone = formatPhoneNumber(phone);
            $(this).val(formattedPhone);
        });

        function formatPhoneNumber(phone) {
            phone = phone.replace(/\D/g, '');
            if (phone.length > 3) {
                var areaCode = phone.substring(0, 3);
                var telephoneNumber = phone.substring(3, 10);
                phone =  "(" + areaCode + ") - " + telephoneNumber;
            }
            return phone;
        }

        $(document).on('click','i[class^="ti ti-eye"]',function(){
           var getType=$(this).parent().parent().find('input').attr('type');
           if(getType=='text'){
               $(this).attr('class','ti ti-eye-off');
               $(this).parent().parent().find('input').attr('type','password');
           }else{
               $(this).attr('class','ti ti-eye');
               $(this).parent().parent().find('input').attr('type','text');
           }
        });
    </script>

    <?php echo $__env->yieldPushContent('js'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Solid-Disk-Direct\Solid-Disk-Direct\resources\views/admin/auth/layouts/app.blade.php ENDPATH**/ ?>