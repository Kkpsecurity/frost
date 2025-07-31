<h5 class="mb-3"><i class="fas fa-heading text-primary"></i> Title & Logo Settings</h5>
<div class="row">
    @php
        $titleLogoKeys = [
            'title',
            'title_prefix',
            'title_postfix',

            'logo',
            'logo_img',
            'logo_img_alt',
            'logo_img_class',

            'auth_logo_enabled',
            'auth_logo_img_path',
            'auth_logo_img_alt',
            'auth_logo_img_class',
            'auth_logo_img_width',
            'auth_logo_img_height',

            'logo_img_xl',
            'logo_img_xl_class',
        ];
    @endphp

    @foreach ($titleLogoKeys as $key)
        <div class="col-md-6">
            @include('admin.admin-center.settings.partials.setting-field', [
                'key' => $key,
                'value' => $adminlteSettings[$key] ?? '',
            ])
        </div>
    @endforeach
</div>
