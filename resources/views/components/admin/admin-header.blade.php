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

<div class="admin-header-wrapper">
    <div class="admin-header-content">
        <div class="d-flex justify-content-between align-items-center admin-header-flex">
            <div>
                <h2 class="page-title">
                    <i class="fas fa-circle page-title-icon"></i>{{ $pageTitle }}
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
