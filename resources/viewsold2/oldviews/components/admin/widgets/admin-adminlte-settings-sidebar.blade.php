@props(['activeTab' => 'title-logo', 'adminlteSettings' => []])

<div class="card card-info mt-3">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-paint-brush"></i> Theme Preview
        </h3>
    </div>
    <div class="card-body">
        <h6><strong>Current Settings Count:</strong></h6>
        <div class="mb-3">
            <span class="badge badge-primary">{{ count($adminlteSettings) }} Total Settings</span>
        </div>

        <h6><strong>Settings by Category:</strong></h6>
        <div class="mb-2 d-flex justify-content-between align-items-center">
            <small class="text-muted">
            <i class="fas fa-heading"></i> Title & Logo:
            </small>
            <span class="badge badge-secondary">
            {{ collect($adminlteSettings)->keys()->filter(function ($key) {
                return in_array($key, [
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
                ]);
                })->count() }}
            </span>
        </div>
        <div class="mb-2 d-flex justify-content-between align-items-center">
            <small class="text-muted">
            <i class="fas fa-th-large"></i> Layout:
            </small>
            <span class="badge badge-secondary">
            {{ collect($adminlteSettings)->keys()->filter(function ($key) {
                return str_starts_with($key, 'layout_') ||
                    in_array($key, [
                    'usermenu_enabled',
                    'usermenu_header',
                    'usermenu_header_class',
                    'usermenu_image',
                    'usermenu_desc',
                    'usermenu_profile_url',
                    'preloader_enabled',
                    'preloader_mode',
                    ]);
                })->count() }}
            </span>
        </div>
        <div class="mb-2 d-flex justify-content-between align-items-center">
            <small class="text-muted">
            <i class="fas fa-lock"></i> Authentication:
            </small>
            <span class="badge badge-secondary">
            {{ collect($adminlteSettings)->keys()->filter(function ($key) {
                return str_starts_with($key, 'classes_auth_') ||
                    str_starts_with($key, 'auth_') ||
                    in_array($key, [
                    'login_url',
                    'register_url',
                    'logout_url',
                    'password_reset_url',
                    'password_email_url',
                    'profile_url',
                    ]);
                })->count() }}
            </span>
        </div>
        <div class="mb-2 d-flex justify-content-between align-items-center">
            <small class="text-muted">
            <i class="fas fa-bars"></i> Sidebar:
            </small>
            <span class="badge badge-secondary">
            {{ collect($adminlteSettings)->keys()->filter(function ($key) {
                return str_starts_with($key, 'sidebar_') || str_starts_with($key, 'right_sidebar_') || $key === 'right_sidebar';
                })->count() }}
            </span>
        </div>
        <div class="mb-2 d-flex justify-content-between align-items-center">
            <small class="text-muted">
            <i class="fas fa-paint-brush"></i> Styling:
            </small>
            <span class="badge badge-secondary">
            {{ collect($adminlteSettings)->keys()->filter(function ($key) {
                return (str_starts_with($key, 'classes_') && !str_starts_with($key, 'classes_auth_')) ||
                    in_array($key, ['google_fonts_allowed', 'use_ico_only', 'use_full_favicon']);
                })->count() }}
            </span>
        </div>
        <div class="mb-2 d-flex justify-content-between align-items-center">
            <small class="text-muted">
            <i class="fas fa-plug"></i> Plugins:
            </small>
            <span class="badge badge-secondary">
            {{ collect($adminlteSettings)->keys()->filter(function ($key) {
                return str_starts_with($key, 'plugins_');
                })->count() }}
            </span>
        </div>
    </div>
</div>

@sysadmin
    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-exclamation-triangle"></i> Important Note
            </h3>
        </div>
        <div class="card-body">
            <p class="text-warning">
                <strong>Theme changes may require:</strong>
            </p>
            <ul class="list-unstyled">
                <li>• Browser refresh</li>
                <li>• Cache clearing</li>
                <li>• CSS recompilation</li>
            </ul>

            <div class="mt-3">
                <button type="button" class="btn btn-sm btn-outline-warning" onclick="clearCache()">
                    <i class="fas fa-trash"></i> Clear Cache
                </button>
            </div>
        </div>
    </div>
@endsysadmin
