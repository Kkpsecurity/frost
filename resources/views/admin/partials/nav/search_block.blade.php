<li class="nav-item">
    <a class="nav-link" data-widget="navbar-search" href="javascript:void(0)" role="button">
        <i class="fas fa-search"></i>
    </a>
    <div class="navbar-search-block">
        <form action="{{ route('admin.services.search',  ['full']) }}" name="site-search" id="site-search" method="post" class="form-inline" role="search">
            @csrf
            <div class="input-group input-group-sm">
                <input class="form-control form-control-navbar" name="search" type="search" placeholder="{{ __('Search') }}" aria-label="{{ __('Search') }}">
                <div class="input-group-append">
                    <button class="btn btn-navbar" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                    <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</li>
