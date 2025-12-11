<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Helpers\SettingHelper;

class AdminLteConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settingHelper = new SettingHelper();

        // Load the default AdminLTE configuration
        $defaultConfig = include config_path('adminlte_config.php');

        $this->command->info('Starting AdminLTE Configuration Seeder...');

        // Title Configuration
        $settingHelper->set('adminlte.title', $defaultConfig['title']);
        $settingHelper->set('adminlte.title_prefix', $defaultConfig['title_prefix']);
        $settingHelper->set('adminlte.title_postfix', $defaultConfig['title_postfix']);
        $this->command->info('✓ Title configuration seeded');

        // Favicon Configuration
        $settingHelper->set('adminlte.use_ico_only', $defaultConfig['use_ico_only']);
        $settingHelper->set('adminlte.use_full_favicon', $defaultConfig['use_full_favicon']);
        $this->command->info('✓ Favicon configuration seeded');

        // Google Fonts
        $settingHelper->set('adminlte.google_fonts_allowed', $defaultConfig['google_fonts']['allowed']);
        $this->command->info('✓ Google fonts configuration seeded');

        // Logo Configuration
        $settingHelper->set('adminlte.logo', $defaultConfig['logo']);
        $settingHelper->set('adminlte.logo_img', $defaultConfig['logo_img']);
        $settingHelper->set('adminlte.logo_img_class', $defaultConfig['logo_img_class']);
        $settingHelper->set('adminlte.logo_img_xl', $defaultConfig['logo_img_xl']);
        $settingHelper->set('adminlte.logo_img_xl_class', $defaultConfig['logo_img_xl_class']);
        $settingHelper->set('adminlte.logo_img_alt', $defaultConfig['logo_img_alt']);
        $this->command->info('✓ Logo configuration seeded');

        // Authentication Logo
        $settingHelper->set('adminlte.auth_logo_enabled', $defaultConfig['auth_logo']['enabled']);
        $settingHelper->set('adminlte.auth_logo_img_path', $defaultConfig['auth_logo']['img']['path']);
        $settingHelper->set('adminlte.auth_logo_img_alt', $defaultConfig['auth_logo']['img']['alt']);
        $settingHelper->set('adminlte.auth_logo_img_class', $defaultConfig['auth_logo']['img']['class']);
        $settingHelper->set('adminlte.auth_logo_img_width', $defaultConfig['auth_logo']['img']['width']);
        $settingHelper->set('adminlte.auth_logo_img_height', $defaultConfig['auth_logo']['img']['height']);
        $this->command->info('✓ Authentication logo configuration seeded');

        // Preloader Configuration
        $settingHelper->set('adminlte.preloader_enabled', $defaultConfig['preloader']['enabled']);
        $settingHelper->set('adminlte.preloader_mode', $defaultConfig['preloader']['mode']);
        $settingHelper->set('adminlte.preloader_img_path', $defaultConfig['preloader']['img']['path']);
        $settingHelper->set('adminlte.preloader_img_alt', $defaultConfig['preloader']['img']['alt']);
        $settingHelper->set('adminlte.preloader_img_effect', $defaultConfig['preloader']['img']['effect']);
        $settingHelper->set('adminlte.preloader_img_width', $defaultConfig['preloader']['img']['width']);
        $settingHelper->set('adminlte.preloader_img_height', $defaultConfig['preloader']['img']['height']);
        $this->command->info('✓ Preloader configuration seeded');

        // User Menu Configuration
        $settingHelper->set('adminlte.usermenu_enabled', $defaultConfig['usermenu_enabled']);
        $settingHelper->set('adminlte.usermenu_header', $defaultConfig['usermenu_header']);
        $settingHelper->set('adminlte.usermenu_header_class', $defaultConfig['usermenu_header_class']);
        $settingHelper->set('adminlte.usermenu_image', $defaultConfig['usermenu_image']);
        $settingHelper->set('adminlte.usermenu_desc', $defaultConfig['usermenu_desc']);
        $settingHelper->set('adminlte.usermenu_profile_url', $defaultConfig['usermenu_profile_url']);
        $this->command->info('✓ User menu configuration seeded');

        // Layout Configuration
        $settingHelper->set('adminlte.layout_topnav', $defaultConfig['layout_topnav']);
        $settingHelper->set('adminlte.layout_boxed', $defaultConfig['layout_boxed']);
        $settingHelper->set('adminlte.layout_fixed_sidebar', $defaultConfig['layout_fixed_sidebar']);
        $settingHelper->set('adminlte.layout_fixed_navbar', $defaultConfig['layout_fixed_navbar']);
        $settingHelper->set('adminlte.layout_fixed_footer', $defaultConfig['layout_fixed_footer']);
        $settingHelper->set('adminlte.layout_dark_mode', $defaultConfig['layout_dark_mode']);
        $this->command->info('✓ Layout configuration seeded');

        // Authentication Classes
        $settingHelper->set('adminlte.classes_auth_card', $defaultConfig['classes_auth_card']);
        $settingHelper->set('adminlte.classes_auth_header', $defaultConfig['classes_auth_header']);
        $settingHelper->set('adminlte.classes_auth_body', $defaultConfig['classes_auth_body']);
        $settingHelper->set('adminlte.classes_auth_footer', $defaultConfig['classes_auth_footer']);
        $settingHelper->set('adminlte.classes_auth_icon', $defaultConfig['classes_auth_icon']);
        $settingHelper->set('adminlte.classes_auth_btn', $defaultConfig['classes_auth_btn']);
        $this->command->info('✓ Authentication classes configuration seeded');

        // Admin Panel Classes
        $settingHelper->set('adminlte.classes_body', $defaultConfig['classes_body']);
        $settingHelper->set('adminlte.classes_brand', $defaultConfig['classes_brand']);
        $settingHelper->set('adminlte.classes_brand_text', $defaultConfig['classes_brand_text']);
        $settingHelper->set('adminlte.classes_content_wrapper', $defaultConfig['classes_content_wrapper']);
        $settingHelper->set('adminlte.classes_content_header', $defaultConfig['classes_content_header']);
        $settingHelper->set('adminlte.classes_content', $defaultConfig['classes_content']);
        $settingHelper->set('adminlte.classes_sidebar', $defaultConfig['classes_sidebar']);
        $settingHelper->set('adminlte.classes_sidebar_nav', $defaultConfig['classes_sidebar_nav']);
        $settingHelper->set('adminlte.classes_topnav', $defaultConfig['classes_topnav']);
        $settingHelper->set('adminlte.classes_topnav_nav', $defaultConfig['classes_topnav_nav']);
        $settingHelper->set('adminlte.classes_topnav_container', $defaultConfig['classes_topnav_container']);
        $this->command->info('✓ Admin panel classes configuration seeded');

        // Sidebar Configuration
        $settingHelper->set('adminlte.sidebar_mini', $defaultConfig['sidebar_mini']);
        $settingHelper->set('adminlte.sidebar_collapse', $defaultConfig['sidebar_collapse']);
        $settingHelper->set('adminlte.sidebar_collapse_auto_size', $defaultConfig['sidebar_collapse_auto_size']);
        $settingHelper->set('adminlte.sidebar_collapse_remember', $defaultConfig['sidebar_collapse_remember']);
        $settingHelper->set('adminlte.sidebar_collapse_remember_no_transition', $defaultConfig['sidebar_collapse_remember_no_transition']);
        $settingHelper->set('adminlte.sidebar_scrollbar_theme', $defaultConfig['sidebar_scrollbar_theme']);
        $settingHelper->set('adminlte.sidebar_scrollbar_auto_hide', $defaultConfig['sidebar_scrollbar_auto_hide']);
        $settingHelper->set('adminlte.sidebar_nav_accordion', $defaultConfig['sidebar_nav_accordion']);
        $settingHelper->set('adminlte.sidebar_nav_animation_speed', $defaultConfig['sidebar_nav_animation_speed']);
        $this->command->info('✓ Sidebar configuration seeded');

        // Right Sidebar Configuration
        $settingHelper->set('adminlte.right_sidebar', $defaultConfig['right_sidebar']);
        $settingHelper->set('adminlte.right_sidebar_icon', $defaultConfig['right_sidebar_icon']);
        $settingHelper->set('adminlte.right_sidebar_theme', $defaultConfig['right_sidebar_theme']);
        $settingHelper->set('adminlte.right_sidebar_slide', $defaultConfig['right_sidebar_slide']);
        $settingHelper->set('adminlte.right_sidebar_push', $defaultConfig['right_sidebar_push']);
        $settingHelper->set('adminlte.right_sidebar_scrollbar_theme', $defaultConfig['right_sidebar_scrollbar_theme']);
        $settingHelper->set('adminlte.right_sidebar_scrollbar_auto_hide', $defaultConfig['right_sidebar_scrollbar_auto_hide']);
        $this->command->info('✓ Right sidebar configuration seeded');

        // URLs Configuration
        $settingHelper->set('adminlte.use_route_url', $defaultConfig['use_route_url']);
        $settingHelper->set('adminlte.dashboard_url', $defaultConfig['dashboard_url']);
        $settingHelper->set('adminlte.logout_url', $defaultConfig['logout_url']);
        $settingHelper->set('adminlte.login_url', $defaultConfig['login_url']);
        $settingHelper->set('adminlte.register_url', $defaultConfig['register_url']);
        $settingHelper->set('adminlte.password_reset_url', $defaultConfig['password_reset_url']);
        $settingHelper->set('adminlte.password_email_url', $defaultConfig['password_email_url']);
        $settingHelper->set('adminlte.profile_url', $defaultConfig['profile_url']);
        $settingHelper->set('adminlte.disable_darkmode_routes', $defaultConfig['disable_darkmode_routes']);
        $this->command->info('✓ URLs configuration seeded');

        // Laravel Asset Bundling
        $settingHelper->set('adminlte.laravel_asset_bundling', $defaultConfig['laravel_asset_bundling']);
        $this->command->info('✓ Laravel asset bundling configuration seeded');

        // Menu Configuration (as JSON)
        $settingHelper->set('adminlte.menu', json_encode($defaultConfig['menu']));
        $this->command->info('✓ Menu configuration seeded');

        // Menu Filters (as JSON)
        $settingHelper->set('adminlte.menu_filters', json_encode($defaultConfig['filters']));
        $this->command->info('✓ Menu filters configuration seeded');

        // Plugins Configuration
        foreach ($defaultConfig['plugins'] as $pluginName => $pluginConfig) {
            $settingHelper->set("adminlte.plugins_{$pluginName}_active", $pluginConfig['active']);
            $settingHelper->set("adminlte.plugins_{$pluginName}_files", json_encode($pluginConfig['files']));
        }
        $this->command->info('✓ Plugins configuration seeded');

        // IFrame Configuration
        $settingHelper->set('adminlte.iframe_default_tab_url', $defaultConfig['iframe']['default_tab']['url']);
        $settingHelper->set('adminlte.iframe_default_tab_title', $defaultConfig['iframe']['default_tab']['title']);
        $settingHelper->set('adminlte.iframe_buttons_close', $defaultConfig['iframe']['buttons']['close']);
        $settingHelper->set('adminlte.iframe_buttons_close_all', $defaultConfig['iframe']['buttons']['close_all']);
        $settingHelper->set('adminlte.iframe_buttons_close_all_other', $defaultConfig['iframe']['buttons']['close_all_other']);
        $settingHelper->set('adminlte.iframe_buttons_scroll_left', $defaultConfig['iframe']['buttons']['scroll_left']);
        $settingHelper->set('adminlte.iframe_buttons_scroll_right', $defaultConfig['iframe']['buttons']['scroll_right']);
        $settingHelper->set('adminlte.iframe_buttons_fullscreen', $defaultConfig['iframe']['buttons']['fullscreen']);
        $settingHelper->set('adminlte.iframe_options_loading_screen', $defaultConfig['iframe']['options']['loading_screen']);
        $settingHelper->set('adminlte.iframe_options_auto_show_new_tab', $defaultConfig['iframe']['options']['auto_show_new_tab']);
        $settingHelper->set('adminlte.iframe_options_use_navbar_items', $defaultConfig['iframe']['options']['use_navbar_items']);
        $this->command->info('✓ IFrame configuration seeded');

        // Livewire Configuration
        $settingHelper->set('adminlte.livewire', $defaultConfig['livewire']);
        $this->command->info('✓ Livewire configuration seeded');

        $this->command->info('');
        $this->command->info('🎉 AdminLTE Configuration Seeder completed successfully!');
        $this->command->info('💡 All default configuration values have been imported into the database.');
        $this->command->info('🔧 You can now customize these settings through the admin interface at:');
        $this->command->info('   http://frost.test/admin/admin-center/settings/adminlte/config');
        $this->command->info('');

        // Count total settings
        $totalSettings = DB::table('settings')->where('key', 'like', 'adminlte.%')->count();
        $this->command->info("📊 Total AdminLTE settings in database: {$totalSettings}");
    }
}
