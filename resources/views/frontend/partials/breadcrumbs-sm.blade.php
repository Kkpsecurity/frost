<!-- Page Header Start -->
<div class="container-fluid page-header bg-dark mb-0 wow fadeIn" data-wow-delay="0.1s">
     <div class="row n-gutters">
          <div class="col-8">
               <h3 class="bc-title p-2 animated slideInDown">{{ $content['title'] }}</h3>
          </div>
          <div class="col-4">
               <nav aria-label="breadcrumb animated slideInDown">
                    <ol class="breadcrumb justify-content-end mb-0 w-auto float-end mt-3">
                         <li class="breadcrumb-item"><a class="" href="{{ route('pages') }}">{{ __('Home') }}</a></li>

                         @foreach(Request::segments() as $segment)
                             @if(!is_numeric($segment))
                                <li class="breadcrumb-item">
                                    <a href="{{ url(join('/', array_slice(Request::segments(), 0, $loop->index + 1))) }}">
                                        {{ ucfirst(humanize($segment)) }}
                                    </a>
                                </li>
                             @endif
                         @endforeach
                    </ol>
               </nav>
          </div>
     </div>
</div>
