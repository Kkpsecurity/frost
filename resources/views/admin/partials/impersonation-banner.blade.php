@if(auth()->check() && auth()->user()->isImpersonated())
<div class="alert alert-warning alert-dismissible fade show impersonation-banner" role="alert" style="margin-bottom: 0; border-radius: 0;">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-10">
                <i class="fas fa-user-secret mr-2"></i>
                <strong>Impersonation Mode:</strong>
                You are currently impersonating <strong>{{ auth()->user()->fname }} {{ auth()->user()->lname }}</strong>
                ({{ auth()->user()->email }})
            </div>
            <div class="col-md-2 text-right">
                <a href="{{ route('admin.stop-impersonating') }}" class="btn btn-sm btn-outline-dark">
                    <i class="fas fa-sign-out-alt mr-1"></i>
                    Stop Impersonating
                </a>
            </div>
        </div>
    </div>
</div>
@endif
