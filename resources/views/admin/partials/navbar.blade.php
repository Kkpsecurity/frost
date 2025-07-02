<nav class="main-header navbar navbar-expand navbar-white navbar-light">

    @include('admin.partials.nav.push_menu')

    <ul class="navbar-nav ml-auto">

        <li class="nav-item">
            <a class="nav-link" data-widget="frontend" href="{{ url('/') }}" role="link" target="_blank" data-toggle="tooltip" data-placement="right" title="Home Page">
                <i class="fas fa-home"></i>
            </a>
        </li>

        @include('admin.partials.nav.search_block')
        @include('admin.partials.nav.notification')

        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="javascript:void(0)" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>

        @include('admin.partials.nav.messages')

    </ul>
</nav>
