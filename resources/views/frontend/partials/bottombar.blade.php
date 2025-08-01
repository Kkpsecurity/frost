
<?php
$segments = request()->segments();
$segment1 = isset($segments[0]) ? $segments[0] : null;
$segment2 = isset($segments[1]) ? $segments[1] : null;
$companyName = RCache::SiteConfig('site_company_name');

$menuItems = [
    [
        'label' => 'Home',
        'url' => route('pages'),
        'isActive' => $segment1 === 'pages' && $segment2 === null,
    ],
    [
        'label' => 'Courses & Schedules',
        'url' => url('courses'),
        'isActive' => $segment1 === 'courses',
        'subMenu' => [
            [
                'label' => 'Courses',
                'url' => url('courses/'),
                'isActive' => $segment1 === 'courses' && $segment2 !== 'schedules',
            ],
            [
                'label' => 'Schedules',
                'url' => url('courses/schedules'),
                'isActive' => $segment1 === 'courses' && $segment2 === 'schedules',
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
                'label' => 'Ensuring Compliance ',
                'url' => url('blog/ensuring-compliance'),
                'isActive' => $segment1 === 'blog' && $segment2 === 'ensuring-compliance',
            ]
        ]
    ],
    [
        'label' => 'Student',
        'url' => "#",
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
                'isActive' => "",
            ],
        ]
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
?>

<div class="header-area ">
    <div class="container-sm-fluid">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-6 order-1 order-md-1">
                <div class="logo">
                    <a class="navbar-brand page-scroll" href="{{ route('pages') }}">
                        <div class="desktop-view">
                            <img src="{{ asset('assets/img/logo/logo.png') }}" alt="{{ $companyName }}" />
                        </div>
                        <div class="mobile-view">
                            <img src="{{ asset('assets/img/logo/logo.png') }}" width="260px" alt="{{ $companyName }}" />
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-lg-7 col-md-7 col-sm-6 order-2 order-md-2">
                <nav class="navbar navbar-expand-lg">
                    <div class="collapse navbar-collapse" id="navbar-main">
                        <ul class="navbar-nav ms-auto mb-lg-0">
                            @isAnyAdmin
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ Auth::user()->Dashboard() }}" aria-label="Admin">Admin</a>
                                </li>
                            @endisAnyAdmin

                            @foreach ($menuItems as $menuItem)
                            @if (isset($menuItem['subMenu']))
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle pages{{ $menuItem['isActive'] ? ' active' : '' }}"
                                        href="{{ $menuItem['url'] }}" role="button" id="dropdownMenuLink"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                        aria-label="{{ $menuItem['label'] }}">
                                        {{ $menuItem['label'] }}
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                        @foreach ($menuItem['subMenu'] as $subItem)
                                            <a class="dropdown-item{{ $subItem['isActive'] ? ' active' : '' }}"
                                                href="{{ $subItem['url'] }}" aria-label="{{ $subItem['label'] }}">
                                                {{ $subItem['label'] }}
                                            </a>
                                        @endforeach
                                    </div>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a class="nav-link pages{{ $menuItem['isActive'] ? ' active' : '' }}"
                                        href="{{ $menuItem['url'] }}" aria-label="{{ $menuItem['label'] }}">
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
