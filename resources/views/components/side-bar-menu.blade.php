<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{ route('dashboard') }}" class="app-brand-link">
      @if(isset(settings()->black_logo) && !empty(settings()->black_logo))
        <img src="{{ asset('storage').'/'.settings()->black_logo }}" width="130px" class="img-fluid light-logo img-logo" alt="{{ settings()->name }}" />
      @else
        <img src="{{ asset('storage/images/default.png') }}" width="130px" class="img-fluid light-logo img-logo" alt="Default" />
      @endif
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
      <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
    </a>
  </div>

  <div class="menu-inner-shadow"></div>
  
  <ul class="menu-inner py-1">
    @if(isset(settings()->website_url) && !empty(settings()->website_url))
      <li class="menu-item">
        <a href="{{ settings()->website_url }}" class="menu-link" target="blank">
            <i class="menu-icon tf-icons ti ti-world"></i>
            <div>Go to Site</div>
        </a>
      </li>
    @endif
    <li class="menu-item {{ request()->is('dashboard')?'active':'' }}">
      <a href="{{ url('/dashboard') }}" class="menu-link">
          <i class="menu-icon tf-icons ti ti-smart-home"></i>
          <div>Dashboards</div>
      </a>
    </li>
    @canany(['roles-list', 'permissions-list', 'menus-list', 'api_docs-list'])
      <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Settings</span>
      </li>
      <li class="menu-item
          {{
              request()->is('menus') ||
              request()->is('menus/*') ||
              request()->is('roles') ||
              request()->is('roles/*') ||
              request()->is('permissions/*') ||
              request()->is('permissions')
              ?'open active':''
          }}
      ">
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons ti ti-settings"></i>
            <div data-i18n="Roles & Permissions">Roles & Permissions</div>
          </a>
          <ul class="menu-sub">
              @can('api_docs-list')
              <li class="menu-item {{ request()->is('api_docs') ?'active':'' }}">
                <a href="{{ route('api_docs.index') }}" class="menu-link" target="blank">
                    <div>Api Docs</div>
                </a>
              </li>
              @endcan
              @can('menus-list')
              <li class="menu-item {{ request()->is('menus') || request()->is('menus/*')?'active':'' }}">
                  <a href="{{ route('menus.index') }}" class="menu-link">
                      <div>Menus</div>
                  </a>
              </li>
              @endcan
              @can('roles-list')
              <li class="menu-item {{ request()->is('roles') || request()->is('roles/*')?'active':'' }}">
                  <a href="{{ route('roles.index') }}" class="menu-link">
                      <div>Roles</div>
                  </a>
              </li>
              @endcan
              @can('permissions-list')
              <li class="menu-item {{ request()->is('permissions') || request()->is('permissions/*')?'active':'' }}">
                  <a href="{{ route('permissions.index') }}" class="menu-link">
                      <div>
                        Permissions
                        @if(count(getNewMenus()) > 0)
                          <span class="blink-text">&#9733;</span>
                        @endif
                      </div>
                  </a>
              </li>
              @endcan
          </ul>
      </li>
    @endcanany

    @php 
      $menuGroups = getDynamicMenuGroups();
    @endphp 

    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Administration</span>
    </li>
    @foreach ($menuGroups as $menuGroup)  
      @php
        $menus = $menuGroup['has_child_menus'];
      @endphp

      <!-- Top-level menu item to group all dynamic menus -->
      @if(isset($menus) && !empty($menus))
        <li class="menu-item
            {{
              in_array(request()->path(), array_map(fn($menu) => str_replace('-', '_', Str::kebab(Str::plural($menu))), $menus)) ? 'open active' : ''
            }}
        ">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons {{ $menuGroup['icon'] ?? 'ti ti-smart-home' }}"></i>
                <div>{{ $menuGroup['menu'] }}</div>
            </a>

            <ul class="menu-sub">
              @foreach ($menus as $menu)
                  @php
                    $pluralMenu = str_replace('-', '_', Str::kebab(Str::plural($menu)));
                    $permission = $pluralMenu . '-list';
                    $route = $pluralMenu; // Plural route, e.g., 'categories', 'brands', etc.
                  @endphp

                  @canany([$permission])
                      <li class="menu-item {{ request()->is($route) || request()->is($route . '/*') ? 'active' : '' }}">
                          <a href="{{ route($route . '.index') }}" class="menu-link">
                              <div>All {{ Str::title(str_replace('_', ' ', Str::plural($menu))) }}</div>
                          </a>
                      </li>
                  @endcanany
              @endforeach
            </ul>
        </li>
      @endif
    @endforeach
  </ul>
</aside>
