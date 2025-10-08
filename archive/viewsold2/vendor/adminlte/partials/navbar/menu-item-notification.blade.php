{{-- Navbar Notification Menu Item --}}
<li @isset($item['id']) id="{{ $item['id'] }}" @endisset class="nav-item dropdown">

    {{-- Notification toggler --}}
    <a class="nav-link" href="#" data-toggle="dropdown"
       @isset($item['id']) id="{{ $item['id'] }}" @endisset>

        {{-- Icon --}}
        <i class="{{ $item['icon'] ?? 'far fa-bell' }}"></i>

        {{-- Badge (hidden by default, will be shown by JavaScript) --}}
        <span class="badge badge-{{ $item['badge_color'] ?? 'warning' }} navbar-badge"
              id="{{ $item['id'] }}-count" style="display: none;">0</span>
    </a>

    {{-- Dropdown Menu --}}
    <div class="dropdown-menu dropdown-menu-{{ $item['dropdown_mode'] ?? 'lg' }} dropdown-menu-right"
         id="{{ $item['id'] }}-dropdown">

        {{-- Header --}}
        <span class="dropdown-item dropdown-header" id="{{ $item['id'] }}-header">
            {{ $item['dropdown_header'] ?? 'Notifications' }}
        </span>
        <div class="dropdown-divider"></div>

        {{-- Content List --}}
        <div id="{{ $item['id'] }}-list" class="thin-scrollbar" style="max-height: 300px; overflow-y: auto;">
            <div class="text-center p-3">
                <div class="loading-spinner"></div>
                <div class="small text-muted mt-2">Loading...</div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item dropdown-footer" id="{{ $item['id'] }}-footer">
            {{ $item['dropdown_footer'] ?? 'View All' }}
        </a>
    </div>

</li>

{{-- Include CSS and JS on first notification item --}}
@if(!isset($__topbar_notification_assets_included))
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/topbar-notifications.css') }}">
    @endpush

    @push('scripts')
        <script src="{{ asset('js/topbar-notifications.js') }}"></script>
    @endpush

    @php $__topbar_notification_assets_included = true; @endphp
@endif
