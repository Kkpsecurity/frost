@props(['activeTab' => 'title-logo', 'adminlteSettings' => []])

<form action="/admin/admin-center/settings/adminlte/config" method="POST">
    @csrf
    @method('PUT')
    <div class="card-body">
        <div class="tab-content" id="adminlte-tabContent">

            <!-- Title & Logo Tab -->
            <div class="tab-pane fade show active" id="title-logo" role="tabpanel" aria-labelledby="title-logo-tab">
                <h5 class="mb-3"><i class="fas fa-heading text-primary"></i> Title & Logo Settings</h5>
                <div class="row">
                    @foreach ($adminlteSettings as $key => $value)
                        @if (in_array($key, [
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
                            ]))
                            <div class="col-md-6">
                                @include('admin.admin-center.settings.partials.setting-field', [
                                    'key' => $key,
                                    'value' => $value,
                                ])
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Layout Tab -->
            <div class="tab-pane fade" id="layout" role="tabpanel" aria-labelledby="layout-tab">
                <h5 class="mb-3"><i class="fas fa-th-large text-success"></i> Layout Settings</h5>
                <div class="row">
                    @foreach ($adminlteSettings as $key => $value)
                        @if (str_starts_with($key, 'layout_') ||
                                in_array($key, [
                                    'usermenu_enabled',
                                    'usermenu_header',
                                    'usermenu_header_class',
                                    'usermenu_image',
                                    'usermenu_desc',
                                    'usermenu_profile_url',
                                    'preloader_enabled',
                                    'preloader_mode',
                                ]))
                            <div class="col-md-6">
                                @include('admin.admin-center.settings.partials.setting-field', [
                                    'key' => $key,
                                    'value' => $value,
                                ])
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Authentication Tab -->
            <div class="tab-pane fade" id="authentication" role="tabpanel" aria-labelledby="authentication-tab">
                <h5 class="mb-3"><i class="fas fa-lock text-warning"></i> Authentication Settings</h5>
                <div class="row">
                    @foreach ($adminlteSettings as $key => $value)
                        @if (str_starts_with($key, 'classes_auth_') ||
                                str_starts_with($key, 'auth_') ||
                                in_array($key, [
                                    'login_url',
                                    'register_url',
                                    'logout_url',
                                    'password_reset_url',
                                    'password_email_url',
                                    'profile_url',
                                ]))
                            <div class="col-md-6">
                                @include('admin.admin-center.settings.partials.setting-field', [
                                    'key' => $key,
                                    'value' => $value,
                                ])
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Sidebar Tab -->
            <div class="tab-pane fade" id="sidebar" role="tabpanel" aria-labelledby="sidebar-tab">
                <h5 class="mb-3"><i class="fas fa-bars text-info"></i> Sidebar Settings</h5>

                <!-- Test Sidebar Button -->
                <div class="alert alert-info live-preview mb-3">
                    <i class="fas fa-lightbulb"></i>
                    <strong>Live Preview:</strong> Changes to sidebar settings will be applied immediately for testing.
                    <button type="button" class="btn btn-sm btn-outline-info ml-2" onclick="applySidebarPreview()">
                        <i class="fas fa-sync"></i> Apply Current Settings
                    </button>
                </div>

                <div class="row">
                    @foreach ($adminlteSettings as $key => $value)
                        @if (str_starts_with($key, 'sidebar_') || str_starts_with($key, 'right_sidebar_') || $key === 'right_sidebar')
                            <div class="col-md-6">
                                @include('admin.admin-center.settings.partials.setting-field', [
                                    'key' => $key,
                                    'value' => $value,
                                ])
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Styling Tab -->
            <div class="tab-pane fade" id="styling" role="tabpanel" aria-labelledby="styling-tab">
                <h5 class="mb-3"><i class="fas fa-paint-brush text-danger"></i> Styling & Classes</h5>
                <div class="row">
                    @foreach ($adminlteSettings as $key => $value)
                        @if (
                            (str_starts_with($key, 'classes_') && !str_starts_with($key, 'classes_auth_')) ||
                                in_array($key, ['google_fonts_allowed', 'use_ico_only', 'use_full_favicon']))
                            <div class="col-md-6">
                                @include('admin.admin-center.settings.partials.setting-field', [
                                    'key' => $key,
                                    'value' => $value,
                                ])
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Plugins Tab -->
            <div class="tab-pane fade" id="plugins" role="tabpanel" aria-labelledby="plugins-tab">
                <h5 class="mb-3"><i class="fas fa-plug text-purple"></i> Plugin Settings</h5>
                <div class="row">
                    @foreach ($adminlteSettings as $key => $value)
                        @if (str_starts_with($key, 'plugins_'))
                            <div class="col-md-6">
                                @include('admin.admin-center.settings.partials.setting-field', [
                                    'key' => $key,
                                    'value' => $value,
                                ])
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Advanced Tab -->
            <div class="tab-pane fade" id="advanced" role="tabpanel" aria-labelledby="advanced-tab">
                <h5 class="mb-3"><i class="fas fa-cogs text-secondary"></i> Advanced Settings</h5>
                <div class="row">
                    @foreach ($adminlteSettings as $key => $value)
                        @if (
                            !in_array($key, [
                                'title',
                                'title_prefix',
                                'title_postfix',
                                'logo',
                                'logo_img',
                                'logo_img_class',
                                'logo_img_xl',
                                'logo_img_xl_class',
                                'logo_img_alt',
                            ]) &&
                                !str_starts_with($key, 'layout_') &&
                                !str_starts_with($key, 'classes_') &&
                                !str_starts_with($key, 'sidebar_') &&
                                !str_starts_with($key, 'right_sidebar_') &&
                                !str_starts_with($key, 'auth_') &&
                                !str_starts_with($key, 'plugins_') &&
                                !str_starts_with($key, 'usermenu_') &&
                                !str_starts_with($key, 'preloader_') &&
                                !in_array($key, [
                                    'google_fonts_allowed',
                                    'use_ico_only',
                                    'use_full_favicon',
                                    'login_url',
                                    'register_url',
                                    'logout_url',
                                    'password_reset_url',
                                    'password_email_url',
                                    'profile_url',
                                    'right_sidebar',
                                ]))
                            <div class="col-md-6">
                                @include('admin.admin-center.settings.partials.setting-field', [
                                    'key' => $key,
                                    'value' => $value,
                                ])
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    <div class="card-footer">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Update AdminLTE Settings
        </button>
        <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Settings
        </a>
        <button type="button" class="btn btn-info" onclick="previewTheme()">
            <i class="fas fa-eye"></i> Preview Changes
        </button>

        @sysadmin
            <button type="button" class="btn btn-warning" onclick="debugForm()">
                <i class="fas fa-bug"></i> Debug Form
            </button>
            <button type="button" class="btn btn-danger" onclick="debugSettings()">
                <i class="fas fa-database"></i> Debug Database
            </button>
        @endsysadmin
    </div>
</form>
