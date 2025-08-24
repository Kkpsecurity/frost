@php
    $segments   = request()->segments();
    $segment1   = $segments[0] ?? null;
    $segment2   = $segments[1] ?? null;
    $companyName = \App\RCache::SiteConfig('site_company_name');

    $menuItems = [
        [
            'label' => 'Home',
            'url' => route('pages'),
            'isActive' => $segment1 === 'pages' && $segment2 === null,
        ],
        [
            'label' => 'Courses & Schedules',
            'url' => route('courses.index'),
            'isActive' => $segment1 === 'courses' || $segment1 === 'schedules',
            'subMenu' => [
                [
                    'label' => 'Courses',
                    'url' => route('courses.index'),
                    'isActive' => $segment1 === 'courses',
                ],
                [
                    'label' => 'Schedules',
                    'url' => route('courses.schedules'),
                    'isActive' => $segment1 === 'schedules',
                ],
                [
                    'label' => 'Why Security Training',
                    'url' => url('blog/security-training'),
                    'isActive' => $segment1 === 'blog' && $segment2 === 'security-training',
                ],
                [
                    'label' => 'Outline 5N-1.140 Security Officer',
                    'url' => url('blog/security-officer'),
                    'isActive' => $segment1 === 'blog' && $segment2 === 'security-officer',
                ],
                [
                    'label' => 'Ensuring Compliance',
                    'url' => url('blog/ensuring-compliance'),
                    'isActive' => $segment1 === 'blog' && $segment2 === 'ensuring-compliance',
                ],
            ],
        ],
        [
            'label' => 'Student',
            'url' => '#',
            'isActive' => $segment1 === 'classroom',
            'subMenu' => [
                [
                    'label' => 'Virtual Classroom',
                    'url' => url('classroom'),
                    'isActive' => $segment1 === 'classroom',
                ],
                [
                    'label' => 'Training Certificate Search',
                    'url' => 'https://licensing.fdacs.gov/OTR/',
                    'isActive' => false,
                ],
            ],
        ],
        [
            'label' => 'Faqs',
            'url' => route('pages', 'faqs'),
            'isActive' => $segment1 === 'pages' && $segment2 === 'faqs',
        ],
        [
            'label' => 'Contacts',
            'url' => route('pages', 'contact'),
            'isActive' => $segment1 === 'pages' && $segment2 === 'contact',
        ],
    ];
@endphp

<div class="header-area">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-lg-5 col-md-5 col-sm-6 order-1">
                <div class="logo">
                    <a class="navbar-brand page-scroll" href="{{ route('pages') }}" aria-label="Home">
                        <div class="desktop-view">
                            <img src="{{ asset('assets/img/logo/logo.png') }}" alt="{{ $companyName }}" />
                        </div>
                        <div class="mobile-view">
                            <img src="{{ asset('assets/img/logo/logo.png') }}" width="260" alt="{{ $companyName }}" />
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-lg-7 col-md-7 col-sm-6 order-2">
                <nav class="navbar navbar-expand-lg">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-main"
                            aria-controls="navbar-main" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbar-main">
                        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

                            @auth
                                @if (Route::has('admin.dashboard'))
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('admin.dashboard') }}" aria-label="Admin">Admin</a>
                                    </li>
                                @endif
                            @endauth

                            @foreach ($menuItems as $menuItem)
                                @if (!empty($menuItem['subMenu']))
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle pages{{ $menuItem['isActive'] ? ' active' : '' }}"
                                           href="{{ $menuItem['url'] }}"
                                           role="button"
                                           id="dropdownMenuLink-{{ \Illuminate\Support\Str::slug($menuItem['label']) }}"
                                           data-bs-toggle="dropdown"
                                           aria-expanded="false"
                                           aria-label="{{ $menuItem['label'] }}">
                                            {{ $menuItem['label'] }}
                                        </a>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink-{{ \Illuminate\Support\Str::slug($menuItem['label']) }}">
                                            @foreach ($menuItem['subMenu'] as $subItem)
                                                <li>
                                                    <a class="dropdown-item{{ $subItem['isActive'] ? ' active' : '' }}"
                                                       href="{{ $subItem['url'] }}"
                                                       aria-label="{{ $subItem['label'] }}">
                                                        {{ $subItem['label'] }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </li>
                                @else
                                    <li class="nav-item">
                                        <a class="nav-link pages{{ $menuItem['isActive'] ? ' active' : '' }}"
                                           href="{{ $menuItem['url'] }}"
                                           aria-label="{{ $menuItem['label'] }}">
                                            {{ $menuItem['label'] }}
                                        </a>
                                    </li>
                                @endif
                            @endforeach

                        </ul>
                    </div>
                </nav>
            </div>
        </div>
    </div>
</div>
