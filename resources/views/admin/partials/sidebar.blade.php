{{-- Custom Sidebar using AdminLTE Config --}}
<aside class="main-sidebar {{ config('adminlte.classes_sidebar', 'sidebar-dark-primary elevation-4') }}">

    {{-- Sidebar brand logo --}}
    <a href="{{ route('admin.dashboard') }}" class="brand-link">
        <img src="{{ asset('assets/admin/dist/img/AdminLTELogo.png') }}" alt="{{ config('app.name') }}"
            class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">{{ __('Admin Panel') }}</span>
    </a>

    {{-- Sidebar menu --}}
    <div class="sidebar">
        {{-- User info --}}
        @auth('admin')
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ auth('admin')->user()->avatar ?? asset('assets/admin/dist/img/user2-160x160.jpg') }}"
                     class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ auth('admin')->user()->fname }} {{ auth('admin')->user()->lname }}</a>
            </div>
        </div>
        @endauth

        <nav class="pt-2">
            <ul class="nav nav-pills nav-sidebar flex-column {{ config('adminlte.classes_sidebar_nav', '') }}"
                data-widget="treeview" role="menu"
                @if(config('adminlte.sidebar_nav_animation_speed') != 300)
                    data-animation-speed="{{ config('adminlte.sidebar_nav_animation_speed') }}"
                @endif
                @if(!config('adminlte.sidebar_nav_accordion'))
                    data-accordion="false"
                @endif>

                {{-- Build menu from config --}}
                @foreach(config('adminlte.menu', []) as $item)
                    @if(isset($item['header']))
                        {{-- Header item --}}
                        <li class="nav-header">{{ $item['header'] }}</li>
                    @elseif(isset($item['submenu']))
                        {{-- Menu with submenu --}}
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon {{ $item['icon'] ?? 'fas fa-circle' }}"></i>
                                <p>
                                    {{ $item['text'] ?? 'Menu Item' }}
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                @foreach($item['submenu'] as $subitem)
                                    <li class="nav-item">
                                        <a href="{{ isset($subitem['route']) ? route($subitem['route']) : ($subitem['url'] ?? '#') }}"
                                           class="nav-link">
                                            <i class="{{ $subitem['icon'] ?? 'far fa-circle' }} nav-icon"></i>
                                            <p>{{ $subitem['text'] ?? 'Submenu Item' }}</p>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @elseif(isset($item['text']))
                        {{-- Simple menu item --}}
                        <li class="nav-item">
                            <a href="{{ isset($item['route']) ? route($item['route']) : ($item['url'] ?? '#') }}"
                               class="nav-link">
                                <i class="nav-icon {{ $item['icon'] ?? 'fas fa-circle' }}"></i>
                                <p>{{ $item['text'] }}</p>
                            </a>
                        </li>
                    @endif
                @endforeach

            </ul>
        </nav>
    </div>

</aside>
