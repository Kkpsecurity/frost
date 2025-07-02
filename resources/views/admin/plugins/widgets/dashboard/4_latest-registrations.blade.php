<style>
    .reg-his-widget {
        height: 420px;
        overflow: hidden;
        overflow-y: scroll;
        scrollbar-width: thin;
    }
</style>
<div class="card card-dark overflow-hidden">
    <h6 class="card-header ">
        <div class="card-title">
            <b>@lang('Latest Registrations')</b><br>
            For the month of: {{ date('M - Y') }}
        </div>

        @if(count($widgets['latestRegistrations'] ?? []))
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
                <a href="{{ url('admin/students') }}" type="button" class="btn btn-tool">
                    <i class="fas fa-eye"></i>
                </a>
            </div>
        @endif
    </h6>

    <div class="card-body p-0 reg-his-widget bg-gradient shadow">
        @if(count($widgets['latestRegistrations'] ?? []))
            <ul class="list-group">
                @foreach ($widgets['latestRegistrations'] as $user)
                    <li class="list-group-item  px-4 py-3">
                        <a href="{{ url('admin.users.edit', [$user->id, 'profile']) }}" class="d-flex text-dark no-decoration">
                           <div class="row">
                               <div class="col-lg-3">
                                   <img class="rounded-circle" width="40" height="40" src="{{ $user->getAvatar('thumb')}}" style="margin-right: 5px;">
                               </div>
                               <div class="col-lg-9">
                                   <div class="ml-2" style="line-height: 1.2;">
                                       <span class="d-block p-0">{{ $user->fullname() }}</span>
                                       <small style="padding-left: 10px;" class="pl-10 text-muted">{{ $user->CreatedAt('J d, Y') }}</small>
                                   </div>
                               </div>
                           </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-muted">@lang('No records found.')</p>
        @endif
    </div>
</div>
