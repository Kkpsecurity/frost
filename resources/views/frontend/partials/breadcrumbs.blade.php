<section class="page-header">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="page-title">
                    <h3>{{ str_replace(['.', '-', '_'], ' ', $content['title']) }}</h3>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 d-none d-md-block">
                <nav class="breadcrumbs" aria-label="breadcrumbs">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('pages') }}">{{ __('Home') }}</a></li>
                        @foreach (array_slice(Request::segments(), 0, 3) as $segment)
                            @if (!Str::isUuid($segment) && !preg_match('/^[0-9a-f]{64}$/', $segment))
                                <li class="breadcrumb-item">
                                    <a href="{{ url(join('/', array_slice(Request::segments(), 0, $loop->index + 1))) }}">{{ ucfirst(humanize($segment)) }}</a>
                                </li>
                            @endif
                        @endforeach
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>
