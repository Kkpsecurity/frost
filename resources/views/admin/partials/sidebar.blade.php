@php

    $user = Auth::user();
    // 'sysadmin', 'administrator', 'support', 'instructor'
    $user_role_name =  Str::lower($user->role->name);

    // dd($user, $user_role_name);

    $menu_items = [
        [
            'route' => route('admin.dashboard'),
            'url' => url('admin/dashboard'),
            'icon' => 'fas fa-home',
            'label' => 'Dashboard',
            'segment' => 'dashboard',
            'role' => ['open'],
        ],
        [
            'route' => null,
            'url' => '#',
            'icon' => 'fas fa-tachometer-alt',
            'label' => 'Admin Center',
            'segment' => 'center',
            'sub_items' => [
                [
                    'route' => route('admin.center.adminusers'),
                    'url' => url('admin/center/adminusers'),
                    'icon' => 'far fa-circle text-white',
                    'label' => 'Admin Account',
                    'segment' => 'adminusers',
                ],
                [
                    'route' => route('admin.center.server_logs'),
                    'url' => url('admin/center/server_logs'),
                    'icon' => 'far fa-circle text-white',
                    'label' => 'Server Logs',
                    'segment' => 'server_logs',
                ],
            ],
            'role' => ['sysadmin', 'administrator'],
        ],
        [
            'route' => null,
            'url' => '#',
            'icon' => 'fas fa-user-secret',
            'label' => 'SysAdmin',
            'segment' => 'sysadmin',
            'sub_items' => [
                [
                    'route' => route('admin'),
                    'url' => url('admin/sysadmin'),
                    'icon' => 'far fa-circle text-white',
                    'label' => 'SysDashboard',
                    'segment' => 'sysadmin',
                ],
                [
                    'route' => route('admin.temp.site_configs'),
                    'url' => url('admin/temp/site_configs'),
                    'icon' => 'far fa-circle text-white',
                    'label' => 'Site Configs',
                    'segment' => 'sysadmin',
                ],
            ],
            'role' => ['sysadmin'],
        ],
        [
            'route' => route('admin.instructors.dashboard'),
            'url' => url('admin/instructors/dashboard'),
            'icon' => 'fas fa-school',
            'label' => 'Instructors',
            'segment' => 'instructors',
            'role' => ['sysadmin', 'administrator', 'support', 'instructor'],
        ],
        [
            'route' => route('admin.students'),
            'url' => url('admin/students'),
            'icon' => 'fas fa-users',
            'label' => 'Students',
            'segment' => 'students',
            'role' => ['sysadmin', 'administrator', 'support', 'instructor'],
        ],
        [
            'route' => route('admin.courses.dashboard'),
            'url' => url('admin/courses/dashboard'),
            'icon' => 'fas fa-book',
            'label' => 'Courses',
            'segment' => 'courses',
            'role' => ['sysadmin', 'support', 'administrator'],
        ],
        [
            'route' => route('admin.frost-support.dashboard'),
            'url' => url('admin/frost-support/dashboard'),
            'icon' => 'fas fa-headset',
            'label' => 'Frost Support',
            'segment' => 'frost-support',
            'role' => ['sysadmin', 'administrator', 'support'],
        ],

        //
        // begin jonesy temp items
        //
        [
            'route' => route('admin.temp.orders'),
            'url' => url('admin/temp/orders'),
            'icon' => 'fas fa-shopping-bag',
            'label' => 'Orders (Accounting)',
            'segment' => 'orders',
            'role' => ['sysadmin', 'administrator', 'support', 'instructor'],
        ],
        [
            'route' => route('admin.temp.course_auths'),
            'url' => url('admin/temp/course_auths'),
            'icon' => 'fas fa-book',
            'label' => 'Active Courses',
            'segment' => 'courses',
            'role' => ['sysadmin', 'administrator', 'support', 'instructor'],
        ],
        [
            'route' => route('admin.temp.completed_course_auths'),
            'url' => url('admin/temp/completed_course_auths'),
            'icon' => 'fas fa-book',
            'label' => 'Completed Courses',
            'segment' => 'courses',
            'role' => ['sysadmin', 'administrator', 'support', 'instructor'],
        ],
        [
            'route' => route('admin.temp.course_dates'),
            'url' => url('admin/temp/course_dates'),
            'icon' => 'fas fa-book',
            'label' => 'Course Dates',
            'segment' => 'courses',
            'role' => ['sysadmin', 'administrator', 'support', 'instructor'],
        ],
        [
            'route' => route('admin.temp.ranges'),
            'url' => url('admin/temp/ranges'),
            'icon' => 'fas fa-book',
            'label' => 'Ranges',
            'segment' => 'ranges',
            'role' => ['sysadmin', 'administrator', 'support', 'instructor'],
        ],
        [
            'route' => route('admin.temp.discount_codes.clients'),
            'url' => url('admin/temp/discount_codes/clients'),
            'icon' => 'fas fa-book',
            'label' => 'Client Codes',
            'segment' => 'discount_codes',
            'role' => ['sysadmin', 'administrator', 'support', 'instructor'],
        ],
        //
        // end jonesy temp items
        //
        [
            'route' => route('admin.reports.dashboard'),
            'url' => url('admin/reports/dashboard'),
            'icon' => 'fas fa-chart-pie',
            'label' => 'Reports',
            'segment' => 'reports',
            'role' => ['sysadmin', 'administrator', 'instructor'],
        ],
    ];

@endphp


<aside class="main-sidebar sidebar-dark-primary elevation-4">

    <a href="{{ route('admin') }}" class="brand-link">
        <img src="{{ asset('assets/admin/dist/img/AdminLTELogo.png') }}" alt="{{ config('app.name') }}"
            class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">{{ __('Admin Panel') }}</span>
    </a>

    <div class="sidebar">
        @include('admin.partials.logged_in_user')
        <?php $segment2 = Request()->Segment(2); ?>
        <?php $segment3 = Request()->Segment(3); ?>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="true">
                @foreach ($menu_items as $item)

                    @php
                        $isAccessible = in_array('open', (array) @$item['role']) || in_array($user_role_name, (array) @$item['role']);
                        $is_active = $segment2 == $item['segment'] ? 'active' : '';
                        $has_sub_items = isset($item['sub_items']) && !empty($item['sub_items']);
                    @endphp

                    @if ($isAccessible)                        
                        <li class="nav-item {{ $is_active }} {{ $has_sub_items ? 'menu-open' : '' }}">
                            <a href="{{ $item['route'] ?? '#' }}" class="nav-link {{ $is_active }}">
                                <i class="nav-icon {{ $item['icon'] }}"></i>
                                <p>{{ $item['label'] }}</p>
                                @if ($has_sub_items)
                                    <i class="right fas fa-angle-left"></i>
                                @endif
                            </a>
                            @if ($has_sub_items)
                                <ul class="nav nav-treeview">
                                    @foreach ($item['sub_items'] as $sub_item)
                                        @php
                                            $is_sub_active = $segment3 == $sub_item['segment'] ? 'active' : '';
                                        @endphp
                                        <li class="nav-item">
                                            <a href="{{ $sub_item['route'] ?? '#' }}"
                                                class="nav-link {{ $is_sub_active }}">
                                                <i class="far {{ $sub_item['icon'] }} nav-icon"></i>
                                                <p>{{ $sub_item['label'] }}</p>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endif
                @endforeach

            </ul>
        </nav>
    </div>
</aside>
