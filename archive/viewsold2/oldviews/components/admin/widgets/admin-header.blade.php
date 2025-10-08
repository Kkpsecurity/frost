@php
    use Illuminate\Support\Str;

    $segments = request()->segments();
    $title = collect($segments)->map(function ($seg, $i) use ($segments) {
        if (is_numeric($seg) && isset($segments[$i - 1])) {
            $modelName = Str::studly(Str::singular($segments[$i - 1]));
            $modelClass = 'App\\Models\\' . $modelName;
            if (class_exists($modelClass)) {
                $modelInstance = $modelClass::find($seg);
                return $modelInstance
                    ? $modelInstance->title ?? ($modelInstance->name ?? 'ID: ' . $seg)
                    : 'ID: ' . $seg;
            }
        }

        // Handle special cases for admin structure
        if ($seg === 'admin-center') {
            return 'Admin Center';
        }
        if ($seg === 'admin-users') {
            return 'Admin Users';
        }
        if ($seg === 'admin') {
            return 'Admin';
        }

        return Str::title(str_replace('-', ' ', $seg));
    });

    $pageTitle = $title->last();
@endphp

<style>
    .admin-header-wrapper {
        background: #343a40; /* dark header */
        color: #ffffff;
        padding: 1rem 2rem;
        border-bottom: 1px solid #444;
        box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    }

    .admin-header-content {
        max-width: 100%;
    }

    .admin-header-flex {
        gap: 1rem;
    }

    .page-title {
        font-size: 1.8rem;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
    }

    .page-title-icon {
        font-size: 1.3rem;
        margin-right: 0.5rem;
        color: #d9e631; /* accent */
    }

    .breadcrumb-nav {
        background: transparent;
    }

    .breadcrumb-custom {
        margin-bottom: 0;
        background: none;
        padding: 0;
        font-size: 0.9rem;
    }

    .breadcrumb-item + .breadcrumb-item::before {
        content: '/';
        color: #999;
    }

    .breadcrumb-item {
        color: #bbb;
    }

    .breadcrumb-item.active {
        color: #fff;
        font-weight: 500;
    }
</style>

<div class="admin-header-wrapper">
    <div class="admin-header-content">
        <div class="d-flex justify-content-between align-items-center admin-header-flex">
            <div>
                <h2 class="page-title">
                    <i class="fas fa-circle page-title-icon"></i> {{ $pageTitle }}
                </h2>
            </div>
            <nav aria-label="breadcrumb" class="breadcrumb-nav">
                <ol class="breadcrumb breadcrumb-custom">
                    @foreach ($title as $index => $crumb)
                        @if ($index < $title->count() - 1)
                            <li class="breadcrumb-item">{{ $crumb }}</li>
                        @endif
                    @endforeach
                    <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle }}</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
