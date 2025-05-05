<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row align-items-center">
            <li class="nav-item mr-auto">
                <a class="navbar-brand m-0" href="{{ route('dashboard') }}">
                    <img src="{{ asset($setting->admin_logo ?? 'backend/app-assets/images/logo/logo.png') }}"
                        width="40" alt="logo">
                    <h2 class="brand-text">{{ $setting->admin_title ?? 'My Admin' }}</h2>
                </a>
            </li>
            <li class="nav-item nav-toggle">
                <a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse">
                    <i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i>
                    <i class="d-none d-xl-block collapse-toggle-icon font-medium-4  text-primary" data-feather="disc"
                        data-ticon="disc"></i>
                </a>
            </li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('dashboard') }}">
                    <i data-feather="home"></i>
                    <span class="menu-item text-truncate" data-i18n="Analytics">
                        Home
                    </span>
                </a>
            </li>

            <li class="nav-item {{ request()->routeIs('user.list') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="#">
                    <i data-feather="users"></i>
                    <span class="menu-title text-truncate" data-i18n="ag-grid">
                        Users
                    </span>
                </a>
                <ul class="menu-content">
                    <li class="{{ request()->routeIs('user.create') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{ route('user.create') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-item text-truncate" data-i18n="Chartjs">
                                Create User
                            </span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('user.list') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{ route('user.list') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-item text-truncate" data-i18n="Chartjs">
                                User List
                            </span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item {{ request()->routeIs('hobby.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('hobby.index') }}">
                    <i data-feather="help-circle"></i>
                    <span class="menu-title text-truncate" data-i18n="ag-grid">
                        Hobby Tags
                    </span>
                </a>
            </li>

            <li class="nav-item {{ request()->routeIs('post.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('post.index') }}">
                    <i data-feather="help-circle"></i>
                    <span class="menu-title text-truncate" data-i18n="ag-grid">
                        All Post
                    </span>
                </a>
            </li>

            <li class="nav-item {{ request()->routeIs('faq.index') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('faq.index') }}">
                    <i data-feather="help-circle"></i>
                    <span class="menu-title text-truncate" data-i18n="ag-grid">
                        FAQ
                    </span>
                </a>
            </li>


            <li class=" navigation-header">
                <span data-i18n="Charts &amp; Maps">
                    Settings
                </span>
                <i data-feather="more-horizontal"></i>
            </li>

            <li
                class=" nav-item {{ request()->routeIs(['admin.setting.*', 'profile.*', 'dynamicpages.*']) ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="#">
                    <i data-feather="settings"></i>
                    <span class="menu-title text-truncate" data-i18n="Charts">
                        System Settings
                    </span>
                    {{-- <span class="badge badge-light-success badge-pill ml-auto mr-2">
                        4
                    </span> --}}
                </a>
                <ul class="menu-content">
                    <li class="{{ request()->routeIs('profile') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{ route('profile') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-item text-truncate" data-i18n="Chartjs">
                                Profile Settings
                            </span>
                        </a>
                    </li>

                    <li class="{{ request()->routeIs('system.setting') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{ route('system.setting') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-item text-truncate" data-i18n="Chartjs">
                                System Setting
                            </span>
                        </a>
                    </li>

                    <li class="{{ request()->routeIs('admin.setting') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{ route('admin.setting') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-item text-truncate" data-i18n="Chartjs">
                                Admin Setting
                            </span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.setting.mail') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{ route('admin.setting.mail') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-item text-truncate" data-i18n="Chartjs">
                                Mail Setting
                            </span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('dynamicpages.index') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{ route('dynamicpages.index') }}">
                            <i class="mdil mdil-layers"></i>
                            <span class="menu-title text-truncate">
                                Dynamic Pages
                            </span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class=" nav-item">
                <a class="d-flex align-items-center" href="#">
                    <i data-feather="shield"></i>
                    <span class="menu-title text-truncate" data-i18n="Charts">
                        Role Permissions
                    </span>
                </a>
                <ul class="menu-content">
                    <li class="{{ request()->routeIs('admin.role.*') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{ route('admin.role.list') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-item text-truncate" data-i18n="Apex">
                                Role
                            </span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.permissions.list') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{ route('admin.permissions.list') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-item text-truncate" data-i18n="Chartjs">
                                Permissions
                            </span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</div>
