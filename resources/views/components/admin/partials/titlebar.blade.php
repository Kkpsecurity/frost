@props([
    'title' => 'Dashboard',
    'breadcrumbs' => [],
    'icon' => null,
])

<style>
    .admin-titlebar.glass-blue {
        --tb-blue: 0, 123, 255;
        /* Bootstrap primary blue */
        --tb-blue-dark: 0, 86, 179;
        /* Darker blue */
        background: linear-gradient(135deg,
                rgba(var(--tb-blue), .15),
                rgba(var(--tb-blue-dark), .08));
        border: 1px solid rgba(var(--tb-blue), .3);
        border-radius: 16px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, .08);
        backdrop-filter: saturate(160%) blur(6px);
        -webkit-backdrop-filter: saturate(160%) blur(6px);
    }

    .dark-mode .admin-titlebar.glass-blue {
        background: linear-gradient(135deg,
                rgba(17, 70, 163, .25),
                rgba(17, 70, 163, .12));
        border-color: rgba(0, 123, 255, .4);
    }

    /* Enhanced title styling */
    .admin-titlebar h1 {
        font-weight: 600 !important;
        color: #b8860b !important;
        /* Keep the golden text color */
        text-shadow: 0 1px 2px rgba(0, 0, 0, .1);
        letter-spacing: -0.025em;
        font-size: 1.75rem !important;
    }

    .dark-mode .admin-titlebar h1 {
        color: #f5d547 !important;
        /* Brighter gold for dark mode */
    }

    /* Enhanced breadcrumb styling */
    .admin-titlebar .breadcrumb {
        background: rgba(255, 255, 255, .6);
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 500;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .06);
    }

    .dark-mode .admin-titlebar .breadcrumb {
        background: rgba(0, 0, 0, .3);
    }

    .admin-titlebar .breadcrumb-item a {
        color: #b8860b;
        /* Golden color for breadcrumb links */
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .admin-titlebar .breadcrumb-item a:hover {
        color: #d4a017;
        /* Lighter gold on hover */
        text-decoration: none;
        transform: translateY(-1px);
    }

    .admin-titlebar .breadcrumb-item.active {
        color: #b8860b;
        /* Active breadcrumb also golden */
        font-weight: 600;
    }

    .admin-titlebar .breadcrumb-item + .breadcrumb-item::before {
        content: "›";
        color: #adb5bd;
        font-weight: 600;
        font-size: 1.1em;
    }

    /* Icon styling */
    .admin-titlebar .title-icon {
        color: #b8860b;
        /* Keep gold icon */
        filter: drop-shadow(0 1px 2px rgba(0, 0, 0, .1));
        font-size: 1.5rem;
        margin-right: 0.75rem;
    }

    .dark-mode .admin-titlebar .title-icon {
        color: #f5d547;
    }
</style>

<div {{ $attributes->merge(['class' => 'admin-titlebar glass-blue mb-3 p-3 p-md-4']) }} role="region"
    aria-label="Page header">
    <div class="d-flex align-items-center justify-content-between flex-wrap w-100">
        <div class="d-flex align-items-center mb-2 mb-sm-0">
            @if ($icon)
                <i class="{{ $icon }} title-icon"></i>
            @endif
            <h1 class="m-0">{{ $title }}</h1>
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item">
                    <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : url('/admin') }}">Home</a>
                </li>
                @foreach ($breadcrumbs as $i => $crumb)
                    @php
                        $isLast = $i === array_key_last($breadcrumbs);
                        // Support both 'title' (current) and legacy 'name' keys
                        $crumbTitle = $crumb['title'] ?? $crumb['name'] ?? 'Untitled';
                        // Support both 'url' and legacy 'href' keys for links
                        $crumbUrl = $crumb['url'] ?? $crumb['href'] ?? '#';
                    @endphp
                    @if ($isLast)
                        <li class="breadcrumb-item active" aria-current="page">{{ $crumbTitle }}</li>
                    @else
                        <li class="breadcrumb-item">
                            <a href="{{ $crumbUrl }}">{{ $crumbTitle }}</a>
                        </li>
                    @endif
                @endforeach
                @if (empty($breadcrumbs))
                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                @endif
            </ol>
        </nav>
    </div>
</div>
