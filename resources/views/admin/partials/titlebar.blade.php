<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ $content['title'] }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <?php $segments = []; ?>
                    @foreach(Request()->segments() as $key => $segment)
                        @if(!is_numeric($segment) || strtolower($segment) !=  'admin')
                            <li class="breadcrumb-item"><a href="{{ route(Request()->segment(1), array_push($segments, $segment)) }}">{{ ucwords($segment) }}</a></li>
                        @endif
                    @endforeach
                </ol>
            </div>
        </div>
    </div>
</section>
