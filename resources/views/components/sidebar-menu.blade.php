<!-- BEGIN: Sidebar -->
<div class="sidebar-wrapper group w-0 hidden xl:w-[248px] xl:block">
    <div id="bodyOverlay" class="w-screen h-screen fixed top-0 bg-slate-900 bg-opacity-50 backdrop-blur-sm z-10 hidden">
    </div>
    <div class="logo-segment">

        <!-- Application Logo -->
        <x-application-logo />

       
    </div>
    <div id="nav_shadow" class="nav_shadow h-[60px] absolute top-[80px] nav-shadow z-[1] w-full transition-all duration-200 pointer-events-none
      opacity-0"></div>
    <div class="sidebar-menus bg-white dark:bg-slate-800 py-2 px-4 h-[calc(100%-80px)] z-50" id="sidebar_menus">
        <ul class="sidebar-menu">
            <li class="sidebar-menu-title">{{ __('MENU') }}</li>
            <li class="{{ (\Request::route()->getName() == 'dashboards*') ? 'active' : '' }}">
                <a href="#" class="navItem">
                    <span class="flex items-center">
                        <iconify-icon class=" nav-icon" icon="heroicons-outline:home"></iconify-icon>
                        <span>{{ __('Dashboard') }}</span>
                    </span>
                    <iconify-icon class="icon-arrow" icon="heroicons-outline:chevron-right"></iconify-icon>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('dashboard') }}" class="navItem {{ (\Request::route()->getName() == 'dashboard') ? 'active' : '' }}">{{ __('Home') }}</a>
                    </li>
                    <li>
                        <a href="{{ route('notifications.index') }}" class="navItem {{ (\Request::route()->getName() == 'notifications.index') ? 'active' : '' }}">{{ __('Notifications') }}</a>
                    </li>
                    <li>
                        <a href="{{ route('finance.index') }}" class="navItem {{ (\Request::route()->getName() == 'finance.index') ? 'active' : '' }}">{{ __('Finance') }}
                        </a>
                    </li>

                     <li>
                        <a href="{{ route('events.list') }}"
                        class="navItem {{ (\Request::route()->getName() == 'events.list') ? 'active' : '' }}">
                            {{ __('Events') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('expense-groups.index') }}"
                        class="navItem {{ (\Request::route()->getName() == 'expense-groups.index') ? 'active' : '' }}">
                            {{ __('Expenses') }}
                        </a>
                    </li>
                </ul>
            </li>
            <!-- Apps Area -->
            <li class="sidebar-menu-title">{{ __('APPS') }}</li>
            
            <li>
                <a href="{{ route('kanban') }}" class="navItem {{ (\Request::route()->getName() == 'kanban') ? 'active' : '' }}">
                    <span class="flex items-center">
                        <iconify-icon class=" nav-icon" icon="heroicons-outline:view-boards"></iconify-icon>
                        <span>{{ __('Kanban') }}</span>
                    </span>
                </a>
            </li>
         
            <li>
                <a href="{{ route('todo') }}" class="navItem {{ (\Request::route()->getName() == 'todo') ? 'active' : '' }}">
                    <span class="flex items-center">
                        <iconify-icon class=" nav-icon" icon="heroicons-outline:clipboard-check"></iconify-icon>
                        <span>{{ __('Todo') }}</span>
                    </span>
                </a>
            </li>
            <li class="{{ (\Request::route()->getName() == 'project*') ? 'active' : '' }}">
                <a href="javascript:void(0)" class="navItem">
                    <span class="flex items-center">
                        <iconify-icon class=" nav-icon" icon="heroicons-outline:document"></iconify-icon>
                        <span>{{ __('Projects') }}</span>
                    </span>
                    <iconify-icon class="icon-arrow" icon="heroicons-outline:chevron-right"></iconify-icon>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('project') }}" class="{{ (\Request::route()->getName() == 'project') ? 'active' : '' }}">{{ __('Projects') }}</a>
                    </li>
                    <li>
                        <a href="{{ route('project-details') }}" class="{{ (\Request::route()->getName() == 'projectDetails') ? 'active' : '' }}">{{ __('Project Details') }}</a>
                    </li>
                </ul>
            </li>
            <!-- Pages Area -->
            <li class="sidebar-menu-title">{{ __('PAGES') }}</li>
            <!-- Utility -->
            <li class="{{ (\Request::route()->getName() == 'utility*') ? 'active' : '' }}">
                <a href="javascript:void(0)" class="navItem">
                    <span class="flex items-center">
                        <iconify-icon class=" nav-icon" icon="heroicons-outline:view-boards"></iconify-icon>
                        <span>{{ __('Utility') }}</span>
                    </span>
                    <iconify-icon class="icon-arrow" icon="heroicons-outline:chevron-right"></iconify-icon>
                </a>
                <ul class="sidebar-submenu">
                   
            
                    <li>
                        <a href="{{ route('utility.blog') }}" class="{{ (\Request::route()->getName() == 'utility.blog') ? 'active' : '' }}">{{ __('Blog') }}</a>
                    </li>
                
                   
                    
                   
                </ul>
            </li>
         
           
          
           
            
            <!-- Icons -->
            
            <!-- Database -->
            <li>
                <a href="{{ route('database-backups.index') }}"
                   class="navItem {{ (request()->is('database-backups*')) ? 'active' : '' }}">
                    <span class="flex items-center">
                        <iconify-icon class=" nav-icon" icon="iconoir:database-backup"></iconify-icon>
                        <span>{{ __('Database Backup') }}</span>
                    </span>
                </a>
            </li>
            <!-- Settings -->
            <li>
                <a href="{{ route('general-settings.show') }}"
                   class="navItem {{ (request()->is('general-settings*')) || (request()->is('users*')) || (request()->is('roles*')) || (request()->is('profiles*')) || (request()->is('permissions*')) ? 'active' : '' }}">
                    <span class="flex items-center">
                        <iconify-icon class=" nav-icon" icon="material-symbols:settings-outline"></iconify-icon>
                        <span>{{ __('Settings') }}</span>
                    </span>
                </a>
            </li>
        </ul>
        <!-- Upgrade Your Business Plan Card Start -->
        <div class="bg-slate-900 mb-10 mt-24 p-4 relative text-center rounded-2xl text-white" id="sidebar_bottom_wizard">
            <img src="/images/svg/rabit.svg" alt="" class="mx-auto relative -mt-[73px]">
            <div class="mt-6">
                <button class="bg-white hover:bg-opacity-80 text-slate-900 text-sm font-Inter rounded-md w-full block py-2 font-medium">
                    Đăng Xuất
                </button>
            </div>
        </div>
        <!-- Upgrade Your Business Plan Card Start -->
    </div>
</div>
<!-- End: Sidebar -->
