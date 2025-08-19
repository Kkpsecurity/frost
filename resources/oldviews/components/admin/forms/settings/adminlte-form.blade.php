<form action="/admin/admin-center/settings/adminlte/config" method="POST">
    @csrf
    @method('PUT')
    <div class="card-body">
        <div class="tab-content" id="adminlte-tabContent">

            <!-- Title & Logo Tab -->
            <div class="tab-pane fade show active" id="title-logo" role="tabpanel" aria-labelledby="title-logo-tab">
                <x-admin.forms.settings.adminlte-title-logo :adminlteSettings="$adminlteSettings" />
            </div>

            <!-- Layout Tab -->
            <div class="tab-pane fade" id="layout" role="tabpanel" aria-labelledby="layout-tab">
                <h5 class="mb-3"><i class="fas fa-th-large text-success"></i> Layout Settings</h5>
                <div class="row">
                    @php
                        $layoutKeys = array_merge(
                            array_filter(array_keys($adminlteSettings), fn($key) => str_starts_with($key, 'layout_')),
                            [
                                'usermenu_enabled',
                                'usermenu_header',
                                'usermenu_header_class',
                                'usermenu_image',
                                'usermenu_desc',
                                'usermenu_profile_url',
                                'preloader_enabled',
                                'preloader_mode',
                            ]
                        );
                    @endphp

                    @foreach ($layoutKeys as $key)
                        <div class="col-md-6">
                            @include('admin.admin-center.settings.partials.setting-field', [
                                'key' => $key,
                                'value' => $adminlteSettings[$key] ?? '',
                            ])
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Authentication Tab -->
            <div class="tab-pane fade" id="authentication" role="tabpanel" aria-labelledby="authentication-tab">
                <h5 class="mb-3"><i class="fas fa-lock text-warning"></i> Authentication Settings</h5>
                <div class="row">
                    @php
                        $authKeys = array_merge(
                            array_filter(array_keys($adminlteSettings), fn($key) => str_starts_with($key, 'classes_auth_')),
                            array_filter(array_keys($adminlteSettings), fn($key) => str_starts_with($key, 'auth_')),
                            [
                                'login_url',
                                'register_url',
                                'logout_url',
                                'password_reset_url',
                                'password_email_url',
                                'profile_url',
                            ]
                        );
                    @endphp

                    @foreach ($authKeys as $key)

                        <div class="col-md-6">
                            @include('admin.admin-center.settings.partials.setting-field', [
                                'key' => $key,
                                'value' => $adminlteSettings[$key] ?? '',
                            ])
                        </div>

                    @endforeach
                </div>
            </div>

            <!-- Sidebar Tab -->
            <div class="tab-pane fade" id="sidebar" role="tabpanel" aria-labelledby="sidebar-tab">
                <h5 class="mb-3"><i class="fas fa-bars text-info"></i> Sidebar Settings</h5>

                <div class="row">
                    @php
                        // Use explicit list from adminlte_config.php to ensure all settings are always shown
                        $sidebarKeys = [
                            // Main sidebar settings

                            'sidebar_collapse',
                            'sidebar_collapse_remember',

                            'sidebar_collapse_auto_size',
                            'sidebar_collapse_remember_no_transition',

                            'sidebar_mini',
                            'sidebar_scrollbar_theme',

                            'sidebar_scrollbar_auto_hide',
                            'sidebar_nav_animation_speed',

                            'sidebar_nav_accordion',

                            // Right sidebar settings
                            'right_sidebar',
                            'right_sidebar_icon',
                            'right_sidebar_theme',
                            'right_sidebar_slide',
                            'right_sidebar_push',
                            'right_sidebar_scrollbar_theme',
                            'right_sidebar_scrollbar_auto_hide',
                        ];
                    @endphp

                    @foreach ($sidebarKeys as $key)
                        <div class="col-md-6">
                            @include('admin.admin-center.settings.partials.setting-field', [
                                'key' => $key,
                                'value' => $adminlteSettings[$key] ?? config("adminlte.{$key}") ?? '',
                            ])
                        </div>
                   @endforeach

                </div>
            </div>

            <!-- Styling Tab -->
            <div class="tab-pane fade" id="styling" role="tabpanel" aria-labelledby="styling-tab">
                <h5 class="mb-3"><i class="fas fa-paint-brush text-danger"></i> Styling & Classes</h5>
                <div class="row">
                    @php
                        $stylingKeys = array_merge(
                            array_filter(array_keys($adminlteSettings), fn($key) => str_starts_with($key, 'classes_') && !str_starts_with($key, 'classes_auth_')),
                            ['google_fonts_allowed', 'use_ico_only', 'use_full_favicon']
                        );
                    @endphp

                    @foreach ($stylingKeys as $key)
                        <div class="col-md-6">
                            @include('admin.admin-center.settings.partials.setting-field', [
                                'key' => $key,
                                'value' => $adminlteSettings[$key] ?? '',
                            ])
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Plugins Tab -->
            <div class="tab-pane fade" id="plugins" role="tabpanel" aria-labelledby="plugins-tab">
                <h5 class="mb-3"><i class="fas fa-plug text-purple"></i> Plugin Settings</h5>
                <div class="row">
                    @php
                        $pluginKeys = array_filter(array_keys($adminlteSettings), fn($key) => str_starts_with($key, 'plugins_'));
                    @endphp

                    @foreach ($pluginKeys as $key)
                        <div class="col-md-6">
                            @include('admin.admin-center.settings.partials.setting-field', [
                                'key' => $key,
                                'value' => $adminlteSettings[$key] ?? '',
                            ])
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Advanced Tab -->
            <div class="tab-pane fade" id="advanced" role="tabpanel" aria-labelledby="advanced-tab">
                <h5 class="mb-3"><i class="fas fa-cogs text-secondary"></i> Advanced Settings</h5>
                <div class="row">
                    @php
                        // Create exclusion list from all other tabs
                        $excludedKeys = array_merge(
                            // Title & Logo keys
                            [
                                'title',
                                'title_prefix',
                                'title_postfix',
                                'logo',
                                'logo_img',
                                'logo_img_class',
                                'logo_img_xl',
                                'logo_img_xl_class',
                                'logo_img_alt',
                                'auth_logo_enabled',
                                'auth_logo_img_path',
                                'auth_logo_img_alt',
                                'auth_logo_img_class',
                                'auth_logo_img_width',
                                'auth_logo_img_height',
                            ],
                            // Layout & User menu keys
                            array_filter(array_keys($adminlteSettings), fn($key) => str_starts_with($key, 'layout_')),
                            [
                                'usermenu_enabled',
                                'usermenu_header',
                                'usermenu_header_class',
                                'usermenu_image',
                                'usermenu_desc',
                                'usermenu_profile_url',
                                'preloader_enabled',
                                'preloader_mode',
                            ],
                            // Authentication keys
                            array_filter(array_keys($adminlteSettings), fn($key) => str_starts_with($key, 'classes_auth_')),
                            array_filter(array_keys($adminlteSettings), fn($key) => str_starts_with($key, 'auth_')),
                            [
                                'login_url',
                                'register_url',
                                'logout_url',
                                'password_reset_url',
                                'password_email_url',
                                'profile_url',
                            ],
                            // Sidebar keys - use explicit list to match sidebar tab
                            [
                                'sidebar_mini',
                                'sidebar_collapse',
                                'sidebar_collapse_auto_size',
                                'sidebar_collapse_remember',
                                'sidebar_collapse_remember_no_transition',
                                'sidebar_scrollbar_theme',
                                'sidebar_scrollbar_auto_hide',
                                'sidebar_nav_accordion',
                                'sidebar_nav_animation_speed',
                                'right_sidebar',
                                'right_sidebar_icon',
                                'right_sidebar_theme',
                                'right_sidebar_slide',
                                'right_sidebar_push',
                                'right_sidebar_scrollbar_theme',
                                'right_sidebar_scrollbar_auto_hide',
                            ],
                            // Styling keys
                            array_filter(array_keys($adminlteSettings), fn($key) => str_starts_with($key, 'classes_') && !str_starts_with($key, 'classes_auth_')),
                            ['google_fonts_allowed', 'use_ico_only', 'use_full_favicon'],
                            // Plugin keys
                            array_filter(array_keys($adminlteSettings), fn($key) => str_starts_with($key, 'plugins_'))
                        );

                        // Get advanced keys (everything not in other tabs)
                        $advancedKeys = array_diff(array_keys($adminlteSettings), $excludedKeys);
                    @endphp

                    @foreach ($advancedKeys as $key)

                        <div class="col-md-6">
                            @include('admin.admin-center.settings.partials.setting-field', [
                                'key' => $key,
                                'value' => $adminlteSettings[$key] ?? '',
                            ])
                        </div>

                    @endforeach
                </div>
            </div>

        </div>
    </div>

    <div class="card-footer">
        <button type="submit" class="btn btn-primary btn-sm" onclick="submitActiveTabOnly(event)">
            <i class="fas fa-save"></i> Update AdminLTE Settings
        </button>
        <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to Settings
        </a>
        <button type="button" class="btn btn-info btn-sm" onclick="previewTheme()">
            <i class="fas fa-eye"></i> Preview Changes
        </button>

        @sysadmin
            <button type="button" class="btn btn-warning btn-sm" onclick="debugForm()">
                <i class="fas fa-bug"></i> Debug Form
            </button>
            <button type="button" class="btn btn-danger btn-sm" onclick="debugSettings()">
                <i class="fas fa-database"></i> Debug Database
            </button>
        @endsysadmin
    </div>

    <!-- Hidden input to track current tab -->
    <input type="hidden" name="current_tab" id="current_tab" value="">
</form>

<script>
// Initialize tooltips for help buttons
$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip({
        html: true,
        trigger: 'hover focus'
    });
});
</script>
