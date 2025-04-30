<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar" >
  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
      <i class="ti ti-menu-2 ti-sm"></i>
    </a>
  </div>

  <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
    <!-- Search -->
    <div class="navbar-nav align-items-center">
      <div class="nav-item navbar-search-wrapper mb-0">
        <a class="nav-item nav-linkd-flex align-items-center px-0" href="javascript:void(0);">
          Welcome, <?php echo e(Auth::user()->name); ?>

        </a>
      </div>
    </div>
    <!-- /Search -->

    <ul class="navbar-nav flex-row align-items-center ms-auto">
      <!-- Style Switcher -->
      <li class="nav-item me-2 me-xl-0">
        <a class="nav-link style-switcher-toggle hide-arrow" href="javascript:void(0);">
          <i class="ti ti-md"></i>
        </a>
      </li>
      <!--/ Style Switcher -->

      <!-- Notification -->
      <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-1">
        <a
          class="nav-link dropdown-toggle hide-arrow"
          href="javascript:void(0);"
          data-bs-toggle="dropdown"
          data-bs-auto-close="outside"
          aria-expanded="false"
        >
          <i class="ti ti-bell ti-md"></i>
          <span class="badge bg-danger rounded-pill badge-notifications"><?php echo e(auth()->user()->unreadNotifications->count()); ?></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end py-0">
          <li class="dropdown-menu-header border-bottom">
            <div class="dropdown-header d-flex align-items-center py-3">
              <h5 class="text-body mb-0 me-auto">Notification</h5>
              <a
                href="javascript:void(0)"
                class="dropdown-notifications-all text-body"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                title="Mark all as read"
                ><i class="ti ti-mail-opened fs-4"></i
              ></a>
            </div>
          </li>
          <li class="dropdown-notifications-list scrollable-container">
            <ul class="list-group list-group-flush">
              <?php $__currentLoopData = auth()->user()->unreadNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                  <a href="<?php echo e($notification->data['url']); ?>" class="d-flex text-dark text-decoration-none">
                    <div class="flex-shrink-0 me-3">
                      <div class="avatar">
                        <img src="<?php echo e(asset('admin/notification-images')); ?>/<?php echo e($notification->data['icon']); ?>" alt class="h-auto rounded-circle" />
                      </div>
                    </div>
                    <div class="flex-grow-1">
                      <h6 class="mb-1"><?php echo e($notification->data['title']); ?></h6>
                      <p class="mb-0"><?php echo e($notification->data['message']); ?></p>
                      <small class="text-muted"><?php echo e($notification->created_at->diffForHumans()); ?></small>
                    </div>
                    <div class="flex-shrink-0 dropdown-notifications-actions">
                      <span class="badge badge-dot"></span>
                    </div>
                  </a>
                </li>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
          </li>
          <li class="dropdown-menu-footer border-top">
            <a
              href="javascript:void(0);"
              class="dropdown-item d-flex justify-content-center text-primary p-2 h-px-40 mb-1 align-items-center"
            >
              View all notifications
            </a>
          </li>
        </ul>
      </li>
      <!--/ Notification -->

      <!-- User -->
      <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
          <div class="avatar avatar-online">
            <img src="<?php echo e(asset('admin')); ?>/assets/img/avatars/1.png" alt class="h-auto rounded-circle" />
          </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <a class="dropdown-item" href="<?php echo e(route('settings.create')); ?>">
              <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                  <div class="avatar avatar-online">
                    <img src="<?php echo e(asset('admin')); ?>/assets/img/avatars/1.png" alt class="h-auto rounded-circle" />
                  </div>
                </div>
                <div class="flex-grow-1">
                  <?php if(Auth::check()): ?>
                    <span class="fw-semibold d-block"><?php echo e(Auth::user()->name); ?></span>
                    <small class="text-muted"><?php echo e(ucfirst(Auth::user()->role)); ?></small>
                  <?php endif; ?>
                </div>
              </div>
            </a>
          </li>
          <li>
            <div class="dropdown-divider"></div>
          </li>
          <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('setting-create')): ?>
              <li>
                  <a class="dropdown-item" href="<?php echo e(route('settings.create')); ?>">
                      <i class="ti ti-settings me-2 ti-sm"></i>
                      <span class="align-middle">Settings</span>
                  </a>
              </li>
          <?php endif; ?>
          <li>
            <div class="dropdown-divider"></div>
          </li>
          <li>
              <a class="dropdown-item" href="<?php echo e(route('user.logout')); ?>">
                  <i class="ti ti-logout me-2 ti-sm"></i>
                  <span class="align-middle">Log Out</span>
              </a>
          </li>
        </ul>
      </li>
      <!--/ User -->
    </ul>
  </div>

  <!-- Search Small Screens -->
  <div class="navbar-search-wrapper search-input-wrapper d-none">
    <input
      type="text"
      class="form-control search-input container-xxl border-0"
      placeholder="Search..."
      aria-label="Search..."
    />
    <i class="ti ti-x ti-sm search-toggler cursor-pointer"></i>
  </div>
</nav>
<?php /**PATH C:\xampp\htdocs\Solid-Disk-Direct\Solid-Disk-Direct\resources\views/components/nav-bar.blade.php ENDPATH**/ ?>