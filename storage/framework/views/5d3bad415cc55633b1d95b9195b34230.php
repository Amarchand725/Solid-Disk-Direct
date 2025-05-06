<?php $__env->startSection('title', Str::upper($title) .' | '.Str::upper(appName())); ?>
<?php $__env->startSection('content'); ?>
    <div class="authentication-wrapper authentication-cover authentication-bg">
        <div class="authentication-inner row">
        <!-- /Left Text -->
        <div class="d-none d-lg-flex col-lg-7 p-0">
            <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
            <img
                src="<?php echo e(asset('admin/assets/img/illustrations/auth-login-illustration-light.png')); ?>"
                alt="auth-login-cover"
                class="img-fluid my-5 auth-illustration"
                data-app-light-img="illustrations/auth-login-illustration-light.png')}}"
                data-app-dark-img="illustrations/auth-login-illustration-dark.png')}}"
            />

            <img
                src="<?php echo e(asset('admin/assets/img/illustrations/bg-shape-image-light.png')); ?>"
                alt="auth-login-cover"
                class="platform-bg"
                data-app-light-img="illustrations/bg-shape-image-light.png')}}"
                data-app-dark-img="illustrations/bg-shape-image-dark.png')}}"
            />
            </div>
        </div>
        <!-- /Left Text -->

        <!-- Login -->
        <div class="d-flex col-12 col-lg-5 align-items-center p-sm-5 p-4">
            <div class="w-px-400 mx-auto">
            <!-- Logo -->
            <?php if (isset($component)) { $__componentOriginal17644a73d0ce7152dda8ed0d758eb01f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal17644a73d0ce7152dda8ed0d758eb01f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.company-logo','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('company-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal17644a73d0ce7152dda8ed0d758eb01f)): ?>
<?php $attributes = $__attributesOriginal17644a73d0ce7152dda8ed0d758eb01f; ?>
<?php unset($__attributesOriginal17644a73d0ce7152dda8ed0d758eb01f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal17644a73d0ce7152dda8ed0d758eb01f)): ?>
<?php $component = $__componentOriginal17644a73d0ce7152dda8ed0d758eb01f; ?>
<?php unset($__componentOriginal17644a73d0ce7152dda8ed0d758eb01f); ?>
<?php endif; ?>
            <!-- /Logo -->
            <h3 class="mb-1 fw-bold">Welcome to <?php echo e(appName()); ?>! 👋</h3>
            <p class="mb-4">Please sign-in to your account and start the adventure</p>

            <div id="errorMessage"></div>

            <form id="loginForm" action="<?php echo e(route('admin.login')); ?>" method="POST">
                <?php echo csrf_field(); ?>

                <div class="mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        class="form-control"
                        id="email"
                        name="email"
                        placeholder="Enter your email"
                        autofocus
                    />
                    <span id="email_error" class="text-danger error"><?php echo e($errors->first('email')); ?></span>
                </div>
                <div class="mb-3 form-password-toggle">
                    <div class="d-flex justify-content-between">
                        <label class="form-label" for="password">Password <span class="text-danger">*</span></label>

                    </div>
                    <div class="input-group input-group-merge">
                        <input
                        type="password"
                        id="password"
                        class="form-control"
                        name="password"
                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                        aria-describedby="password"
                        />
                        <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                    </div>
                    <span id="password_error" class="text-danger error"><?php echo e($errors->first('password')); ?></span>
                </div>
                <div class="mb-3">
                    <div class="row">
                        <div class="col">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember-me" />
                                <label class="form-check-label" for="remember-me"> Remember Me </label>
                            </div>
                        </div>
                        <div class="col-auto">
                            <a href="<?php echo e(route('password.request')); ?>">
                                <small>Forgot Password?</small>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-12 mt-3 action-btn">
                    <div class="demo-inline-spacing sub-btn">
                        <button type="submit" class="btn btn-primary me-sm-3 me-1 submitBtn">Submit</button>
                    </div>
                    <div class="demo-inline-spacing loading-btn" style="display: none;">
                        <button class="btn btn-primary waves-effect waves-light" type="button" disabled="">
                          <span class="spinner-border me-1" role="status" aria-hidden="true"></span>
                          Loading...
                        </button>
                    </div>
                </div>
            </form>
            </div>
        </div>
        <!-- /Login -->
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('js'); ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.auth.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\solid-dis-direct\backend\resources\views/admin/auth/login.blade.php ENDPATH**/ ?>