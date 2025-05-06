<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="<?php echo e(route('dashboard')); ?>" class="app-brand-link">
      <?php if(isset(settings()->black_logo) && !empty(settings()->black_logo)): ?>
        <img src="<?php echo e(asset('storage').'/'.settings()->black_logo); ?>" width="130px" class="img-fluid light-logo img-logo" alt="<?php echo e(settings()->name); ?>" />
      <?php else: ?>
        <img src="<?php echo e(asset('storage/images/default.png')); ?>" width="130px" class="img-fluid light-logo img-logo" alt="Default" />
      <?php endif; ?>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
      <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
    </a>
  </div>

  <div class="menu-inner-shadow"></div>
  
  <!-- Vertical Progress Bar -->
  <div class="sidebar-progress-bar">
    <div class="sidebar-progress" id="sidebar-progress"></div>
  </div>

  <div class="scroll-container" style="position: relative; overflow-y: auto; height: calc(100vh - 64px);">
    <ul class="menu-inner py-1">
      <?php if(isset(settings()->website_url) && !empty(settings()->website_url)): ?>
        <li class="menu-item">
          <a href="<?php echo e(settings()->website_url); ?>" class="menu-link" target="blank">
              <i class="menu-icon tf-icons ti ti-world"></i>
              <div>Go to Site</div>
          </a>
        </li>
      <?php endif; ?>
      <li class="menu-item <?php echo e(request()->is('dashboard')?'active':''); ?>">
        <a href="<?php echo e(url('/dashboard')); ?>" class="menu-link">
            <i class="menu-icon tf-icons ti ti-smart-home"></i>
            <div>Dashboards</div>
        </a>
      </li>
      <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['roles-list', 'permissions-list', 'menus-list', 'api_docs-list', 'users-list'])): ?>
        <li class="menu-header small text-uppercase">
          <span class="menu-header-text">Settings</span>
        </li>
        <li class="menu-item
            <?php echo e(request()->is('users') ||
                request()->is('users/*') ||
                request()->is('menus') ||
                request()->is('menus/*') ||
                request()->is('roles') ||
                request()->is('roles/*') ||
                request()->is('permissions/*') ||
                request()->is('permissions')
                ?'open active':''); ?>

        ">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
              <i class="menu-icon tf-icons ti ti-settings"></i>
              <div data-i18n="Roles & Permissions">Roles & Permissions</div>
            </a>
            <ul class="menu-sub">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('users-list')): ?>
                <li class="menu-item <?php echo e(request()->is('users') ?'active':''); ?>">
                  <a href="<?php echo e(route('users.index')); ?>" class="menu-link"  >
                      <div>All Users</div>
                  </a>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('api_docs-list')): ?>
                <li class="menu-item <?php echo e(request()->is('api_docs') ?'active':''); ?>">
                  <a href="<?php echo e(route('api_docs.index')); ?>" class="menu-link" target="blank">
                      <div>Api Docs</div>
                  </a>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('menus-list')): ?>
                <li class="menu-item <?php echo e(request()->is('menus') || request()->is('menus/*')?'active':''); ?>">
                    <a href="<?php echo e(route('menus.index')); ?>" class="menu-link">
                        <div>Menus</div>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('roles-list')): ?>
                <li class="menu-item <?php echo e(request()->is('roles') || request()->is('roles/*')?'active':''); ?>">
                    <a href="<?php echo e(route('roles.index')); ?>" class="menu-link">
                        <div>Roles</div>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('permissions-list')): ?>
                <li class="menu-item <?php echo e(request()->is('permissions') || request()->is('permissions/*')?'active':''); ?>">
                    <a href="<?php echo e(route('permissions.index')); ?>" class="menu-link">
                        <div>
                          Permissions
                          <?php if(count(getNewMenus()) > 0): ?>
                            <span class="blink-text">&#9733;</span>
                          <?php endif; ?>
                        </div>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </li>
      <?php endif; ?>

      <?php 
        $menuGroups = getDynamicMenuGroups();
      ?> 

      <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Administration</span>
      </li>
      <?php $__currentLoopData = $menuGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menuGroup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>  
        <?php
          $menus = $menuGroup['has_child_menus'];
        ?>

        <!-- Top-level menu item to group all dynamic menus -->
        <?php if(isset($menus) && !empty($menus)): ?>
          <li class="menu-item
              <?php echo e(in_array(request()->path(), array_map(fn($menu) => str_replace('-', '_', Str::kebab(Str::plural($menu))), $menus)) ? 'open active' : ''); ?>

          ">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                  <i class="menu-icon tf-icons <?php echo e($menuGroup['icon'] ?? 'ti ti-smart-home'); ?>"></i>
                  <div><?php echo e($menuGroup['menu']); ?></div>
              </a>

              <ul class="menu-sub">
                <?php $__currentLoopData = $menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                      $pluralMenu = str_replace('-', '_', Str::kebab(Str::plural($menu)));
                      $permission = $pluralMenu . '-list';
                      $route = $pluralMenu; // Plural route, e.g., 'categories', 'brands', etc.
                    ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any([$permission])): ?>
                        <li class="menu-item <?php echo e(request()->is($route) || request()->is($route . '/*') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route($route . '.index')); ?>" class="menu-link">
                                <div>All <?php echo e(Str::title(str_replace('_', ' ', Str::plural($menu)))); ?></div>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </ul>
          </li>
        <?php endif; ?>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
  </div>
</aside>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const scrollArea = document.querySelector('#layout-menu .scroll-container');
    const progressBar = document.getElementById("sidebar-progress");

    if (scrollArea && progressBar) {
      function updateProgress() {
        const scrollTop = scrollArea.scrollTop;
        const scrollHeight = scrollArea.scrollHeight - scrollArea.clientHeight;
        const percent = scrollHeight > 0 ? (scrollTop / scrollHeight) * 100 : 0;
        progressBar.style.height = percent + "%";
      }

      scrollArea.addEventListener("scroll", updateProgress);
      updateProgress();
    } else {
      console.warn("Progress bar or scroll container not found");
    }
  });
  // Prevent page scroll when scrolling inside sidebar
  scrollArea.addEventListener("wheel", function (e) {
    const delta = e.deltaY;
    const up = delta < 0;
    const down = delta > 0;

    const atTop = scrollArea.scrollTop === 0;
    const atBottom = scrollArea.scrollTop + scrollArea.clientHeight >= scrollArea.scrollHeight;

    if ((up && !atTop) || (down && !atBottom)) {
      e.preventDefault();
      scrollArea.scrollTop += delta;
    }
  }, { passive: false });
</script>
<?php /**PATH C:\xampp\htdocs\solid-dis-direct\backend\resources\views/components/side-bar-menu.blade.php ENDPATH**/ ?>