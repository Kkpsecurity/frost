<?php

namespace App\Support;

use App\Helpers\SettingHelper;

class AdminLteConfigElements {

    protected $settingHelper;

    public function __construct(SettingHelper $settingHelper)
    {
        // Initialize any necessary properties or dependencies
        $this->settingHelper = $settingHelper;
        $this->settingHelper->setPrefix('adminlte');
    }

    // Add class members here as needed
    public function getTitle() {
        return ['title' => $this->settingHelper->get('title', 'Admin Panel')];
    }

    public function getTitlePrefix() {
        return ['title_prefix' => $this->settingHelper->get('title_prefix', '')];
    }

    public function getTitlePostfix() {
        return ['title_postfix' => $this->settingHelper->get('title_postfix', '')];
    }

    public function getUseIcoOnly() {
        return ['use_ico_only' => $this->settingHelper->get('use_ico_only', false)];
    }

    public function getUseFullFavicon() {
        return ['use_full_favicon' => $this->settingHelper->get('use_full_favicon', false)];
    }

    public function getGoogleFonts()
    {
        return [
            'google_fonts' => [
                'allowed' => $this->settingHelper->get('google_fonts.allowed', true),
            ],
        ];
    }

    public function getLogo()
    {
        return ['logo' => $this->settingHelper->get('logo', '<b>Frost</b> Admin')];
    }

    public function getLogoImg()
    {
        return ['logo_img' => $this->settingHelper->get('logo_img', 'images/200851589.jpg')];
    }

    public function getLogoImgClass()
    {
        return ['logo_img_class' => $this->settingHelper->get('logo_img_class', 'brand-image img-circle elevation-3')];
    }

    public function getLogoImgXl()
    {
        return ['logo_img_xl' => $this->settingHelper->get('logo_img_xl', null)];
    }

    public function getLogoImgXlClass()
    {
        return ['logo_img_xl_class' => $this->settingHelper->get('logo_img_xl_class', 'brand-image-xs')];
    }

    public function getLogoImgAlt()
    {
        return ['logo_img_alt' => $this->settingHelper->get('logo_img_alt', 'Frost Admin Logo')];
    }

    public function getAuthLogo()
    {
        return [
            'auth_logo' => [
                'enabled' => $this->settingHelper->get('auth_logo.enabled', true),
                'img' => [
                    'path' => $this->settingHelper->get('auth_logo.img.path', 'images/200851589.jpg'),
                    'alt' => $this->settingHelper->get('auth_logo.img.alt', 'Frost Logo'),
                    'class' => $this->settingHelper->get('auth_logo.img.class', ''),
                    'width' => $this->settingHelper->get('auth_logo.img.width', 50),
                    'height' => $this->settingHelper->get('auth_logo.img.height', 50),
                ],
            ],
        ];
    }

    public function getPreloader()
    {
        return [
            'preloader' => [
                'enabled' => $this->settingHelper->get('preloader.enabled', true),
                'mode' => $this->settingHelper->get('preloader.mode', 'fullscreen'),
                'img' => [
                    'path' => $this->settingHelper->get('preloader.img.path', 'images/200851589.jpg'),
                    'alt' => $this->settingHelper->get('preloader.img.alt', 'Frost Loading...'),
                    'effect' => $this->settingHelper->get('preloader.img.effect', 'animation__shake'),
                    'width' => $this->settingHelper->get('preloader.img.width', 60),
                    'height' => $this->settingHelper->get('preloader.img.height', 60),
                ],
            ],
        ];
    }

    public function getUsermenuEnabled()
    {
        return ['usermenu_enabled' => $this->settingHelper->get('usermenu_enabled', true)];
    }

    public function getUsermenuHeader()
    {
        return ['usermenu_header' => $this->settingHelper->get('usermenu_header', true)];
    }

    public function getUsermenuHeaderClass()
    {
        return ['usermenu_header_class' => $this->settingHelper->get('usermenu_header_class', 'bg-primary')];
    }

    public function getUsermenuImage()
    {
        return ['usermenu_image' => $this->settingHelper->get('usermenu_image', true)];
    }

    public function getUsermenuDesc()
    {
        return ['usermenu_desc' => $this->settingHelper->get('usermenu_desc', true)];
    }

    public function getUsermenuProfileUrl()
    {
        return ['usermenu_profile_url' => $this->settingHelper->get('usermenu_profile_url', false)];
    }

    public function getLayoutTopnav()
    {
        return ['layout_topnav' => $this->settingHelper->get('layout_topnav', null)];
    }

    public function getLayoutBoxed()
    {
        return ['layout_boxed' => $this->settingHelper->get('layout_boxed', null)];
    }

    public function getLayoutFixedSidebar()
    {
        return ['layout_fixed_sidebar' => $this->settingHelper->get('layout_fixed_sidebar', null)];
    }

    public function getLayoutFixedNavbar()
    {
        return ['layout_fixed_navbar' => $this->settingHelper->get('layout_fixed_navbar', null)];
    }

    public function getLayoutFixedFooter()
    {
        return ['layout_fixed_footer' => $this->settingHelper->get('layout_fixed_footer', null)];
    }

    public function getLayoutDarkMode()
    {
        return ['layout_dark_mode' => $this->settingHelper->get('layout_dark_mode', true)];
    }

    public function getClassesAuthCard()
    {
        return ['classes_auth_card' => $this->settingHelper->get('classes_auth_card', 'card-outline card-dark')];
    }

    public function getClassesAuthHeader()
    {
        return ['classes_auth_header' => $this->settingHelper->get('classes_auth_header', 'bg-dark text-light')];
    }

    public function getClassesAuthBody()
    {
        return ['classes_auth_body' => $this->settingHelper->get('classes_auth_body', 'bg-dark text-light')];
    }

    public function getClassesAuthFooter()
    {
        return ['classes_auth_footer' => $this->settingHelper->get('classes_auth_footer', 'bg-dark text-light')];
    }

    public function getClassesAuthIcon()
    {
        return ['classes_auth_icon' => $this->settingHelper->get('classes_auth_icon', '')];
    }

    public function getClassesAuthBtn()
    {
        return ['classes_auth_btn' => $this->settingHelper->get('classes_auth_btn', 'btn-flat btn-light')];
    }

    public function getClassesBody()
    {
        return ['classes_body' => $this->settingHelper->get('classes_body', '')];
    }

    public function getClassesBrand()
    {
        return ['classes_brand' => $this->settingHelper->get('classes_brand', '')];
    }

    public function getClassesBrandText()
    {
        return ['classes_brand_text' => $this->settingHelper->get('classes_brand_text', '')];
    }

    public function getClassesContentWrapper()
    {
        return ['classes_content_wrapper' => $this->settingHelper->get('classes_content_wrapper', '')];
    }

    public function getClassesContentHeader()
    {
        return ['classes_content_header' => $this->settingHelper->get('classes_content_header', '')];
    }

    public function getClassesContent()
    {
        return ['classes_content' => $this->settingHelper->get('classes_content', '')];
    }

    public function getClassesSidebar()
    {
        return ['classes_sidebar' => $this->settingHelper->get('classes_sidebar', 'sidebar-dark-primary elevation-4')];
    }

    public function getClassesSidebarNav()
    {
        return ['classes_sidebar_nav' => $this->settingHelper->get('classes_sidebar_nav', '')];
    }

    public function getClassesTopnav()
    {
        return ['classes_topnav' => $this->settingHelper->get('classes_topnav', 'navbar-dark navbar-dark')];
    }

    public function getClassesTopnavNav()
    {
        return ['classes_topnav_nav' => $this->settingHelper->get('classes_topnav_nav', 'navbar-expand')];
    }

    public function getClassesTopnavContainer()
    {
        return ['classes_topnav_container' => $this->settingHelper->get('classes_topnav_container', 'container')];
    }

    public function getSidebarMini()
    {
        return ['sidebar_mini' => $this->settingHelper->get('sidebar_mini', 'lg')];
    }

    public function getSidebarCollapse()
    {
        return ['sidebar_collapse' => $this->settingHelper->get('sidebar_collapse', false)];
    }

    public function getSidebarCollapseAutoSize()
    {
        return ['sidebar_collapse_auto_size' => $this->settingHelper->get('sidebar_collapse_auto_size', false)];
    }

    public function getSidebarCollapseRemember()
    {
        return ['sidebar_collapse_remember' => $this->settingHelper->get('sidebar_collapse_remember', false)];
    }

    public function getSidebarCollapseRememberNoTransition()
    {
        return ['sidebar_collapse_remember_no_transition' => $this->settingHelper->get('sidebar_collapse_remember_no_transition', true)];
    }

    public function getSidebarScrollbarTheme()
    {
        return ['sidebar_scrollbar_theme' => $this->settingHelper->get('sidebar_scrollbar_theme', 'os-theme-light')];
    }

    public function getSidebarScrollbarAutoHide()
    {
        return ['sidebar_scrollbar_auto_hide' => $this->settingHelper->get('sidebar_scrollbar_auto_hide', 'l')];
    }

    public function getSidebarNavAccordion()
    {
        return ['sidebar_nav_accordion' => $this->settingHelper->get('sidebar_nav_accordion', true)];
    }

    public function getSidebarNavAnimationSpeed()
    {
        return ['sidebar_nav_animation_speed' => $this->settingHelper->get('sidebar_nav_animation_speed', 300)];
    }

    public function getRightSidebar()
    {
        return ['right_sidebar' => $this->settingHelper->get('right_sidebar', true)];
    }

    public function getRightSidebarIcon()
    {
        return ['right_sidebar_icon' => $this->settingHelper->get('right_sidebar_icon', 'fas fa-cogs')];
    }

    public function getRightSidebarTheme()
    {
        return ['right_sidebar_theme' => $this->settingHelper->get('right_sidebar_theme', 'dark')];
    }

    public function getRightSidebarSlide()
    {
        return ['right_sidebar_slide' => $this->settingHelper->get('right_sidebar_slide', true)];
    }

    public function getRightSidebarPush()
    {
        return ['right_sidebar_push' => $this->settingHelper->get('right_sidebar_push', true)];
    }

    public function getRightSidebarScrollbarTheme()
    {
        return ['right_sidebar_scrollbar_theme' => $this->settingHelper->get('right_sidebar_scrollbar_theme', 'os-theme-light')];
    }

    public function getRightSidebarScrollbarAutoHide()
    {
        return ['right_sidebar_scrollbar_auto_hide' => $this->settingHelper->get('right_sidebar_scrollbar_auto_hide', 'l')];
    }

    public function getRightSidebarWidth()
    {
        return ['right_sidebar_width' => $this->settingHelper->get('right_sidebar_width', '500px')];
    }

    public function getUseRouteUrl()
    {
        return ['use_route_url' => $this->settingHelper->get('use_route_url', false)];
    }

    public function getDashboardUrl()
    {
        return ['dashboard_url' => $this->settingHelper->get('dashboard_url', 'home')];
    }

    public function getLogoutUrl()
    {
        return ['logout_url' => $this->settingHelper->get('logout_url', 'logout')];
    }

    public function getLoginUrl()
    {
        return ['login_url' => $this->settingHelper->get('login_url', 'login')];
    }

    public function getRegisterUrl()
    {
        return ['register_url' => $this->settingHelper->get('register_url', 'register')];
    }

    public function getPasswordResetUrl()
    {
        return ['password_reset_url' => $this->settingHelper->get('password_reset_url', 'password/reset')];
    }

    public function getPasswordEmailUrl()
    {
        return ['password_email_url' => $this->settingHelper->get('password_email_url', 'password/email')];
    }

    public function getProfileUrl()
    {
        return ['profile_url' => $this->settingHelper->get('profile_url', false)];
    }

    public function getDisableDarkmodeRoutes()
    {
        return [
            'disable_darkmode_routes' => $this->settingHelper->get('adminlte.disable_darkmode_routes', false),
        ];
    }

    // Laravel Asset Bundling Configuration
    public function getLaravelAssetBundling()
    {
        return [
            'laravel_asset_bundling' => $this->settingHelper->get('adminlte.laravel_asset_bundling', false),
        ];
    }

    // IFrame Configuration
    public function getIframeDefaultTab()
    {
        return [
            'iframe' => [
                'default_tab' => [
                    'url' => $this->settingHelper->get('adminlte.iframe_default_tab_url', null),
                    'title' => $this->settingHelper->get('adminlte.iframe_default_tab_title', null),
                ],
            ],
        ];
    }

    public function getIframeButtons()
    {
        return [
            'iframe' => [
                'buttons' => [
                    'close' => $this->settingHelper->get('adminlte.iframe_buttons_close', true),
                    'close_all' => $this->settingHelper->get('adminlte.iframe_buttons_close_all', true),
                    'close_all_other' => $this->settingHelper->get('adminlte.iframe_buttons_close_all_other', true),
                    'scroll_left' => $this->settingHelper->get('adminlte.iframe_buttons_scroll_left', true),
                    'scroll_right' => $this->settingHelper->get('adminlte.iframe_buttons_scroll_right', true),
                    'fullscreen' => $this->settingHelper->get('adminlte.iframe_buttons_fullscreen', true),
                ],
            ],
        ];
    }

    public function getIframeOptions()
    {
        return [
            'iframe' => [
                'options' => [
                    'loading_screen' => $this->settingHelper->get('adminlte.iframe_options_loading_screen', 1000),
                    'auto_show_new_tab' => $this->settingHelper->get('adminlte.iframe_options_auto_show_new_tab', true),
                    'use_navbar_items' => $this->settingHelper->get('adminlte.iframe_options_use_navbar_items', true),
                ],
            ],
        ];
    }

    public function getIframeConfig()
    {
        return [
            'iframe' => [
                'default_tab' => [
                    'url' => $this->settingHelper->get('adminlte.iframe_default_tab_url', null),
                    'title' => $this->settingHelper->get('adminlte.iframe_default_tab_title', null),
                ],
                'buttons' => [
                    'close' => $this->settingHelper->get('adminlte.iframe_buttons_close', true),
                    'close_all' => $this->settingHelper->get('adminlte.iframe_buttons_close_all', true),
                    'close_all_other' => $this->settingHelper->get('adminlte.iframe_buttons_close_all_other', true),
                    'scroll_left' => $this->settingHelper->get('adminlte.iframe_buttons_scroll_left', true),
                    'scroll_right' => $this->settingHelper->get('adminlte.iframe_buttons_scroll_right', true),
                    'fullscreen' => $this->settingHelper->get('adminlte.iframe_buttons_fullscreen', true),
                ],
                'options' => [
                    'loading_screen' => $this->settingHelper->get('adminlte.iframe_options_loading_screen', 1000),
                    'auto_show_new_tab' => $this->settingHelper->get('adminlte.iframe_options_auto_show_new_tab', true),
                    'use_navbar_items' => $this->settingHelper->get('adminlte.iframe_options_use_navbar_items', true),
                ],
            ],
        ];
    }

    // Livewire Configuration
    public function getLivewire()
    {
        return [
            'livewire' => $this->settingHelper->get('adminlte.livewire', false),
        ];
    }

    // Menu Configuration
    public function getMenuConfig()
    {
        // Default menu structure
        $defaultMenu = [
            // Navbar items:
            [
                'type' => 'navbar-search',
                'text' => 'search',
                'topnav_right' => true,
            ],
            [
                'text' => 'Notifications',
                'icon' => 'far fa-bell',
                'url' => '/messaging/notifications',
                'id' => 'notifications-toggle',
                'topnav_right' => true,
                'classes' => 'nav-notification-item',
            ],
            [
                'text' => 'Messages',
                'icon' => 'far fa-envelope',
                'url' => '/messaging',
                'id' => 'messages-toggle',
                'topnav_right' => true,
                'classes' => 'nav-message-item',
            ],
            [
                'type' => 'fullscreen-widget',
                'topnav_right' => true,
            ],

            // Sidebar items:
            [
                'type' => 'sidebar-menu-search',
                'text' => 'search',
            ],
            [
                'text' => 'Dashboard',
                'route' => 'admin.dashboard',
                'icon' => 'fas fa-fw fa-tachometer-alt',
            ],

            ['header' => 'ADMINISTRATION'],
            [
                'text' => 'Admin Center',
                'icon' => 'fas fa-fw fa-cogs',
                'submenu' => [
                    [
                        'text' => 'Admin Users',
                        'route' => 'admin.admin-center.admin-users.index',
                        'icon' => 'fas fa-fw fa-user-shield',
                    ],
                    [
                        'text' => 'Site Settings',
                        'route' => 'admin.settings.index',
                        'icon' => 'fas fa-fw fa-wrench',
                    ],
                    [
                        'text' => 'Media Manager',
                        'route' => 'admin.media-manager.index',
                        'icon' => 'fas fa-fw fa-hdd',
                    ]
                ],
            ],

            ['header' => 'USER MANAGEMENT'],
            [
                'text' => 'Instructors',
                'url' => 'admin/instructors',
                'icon' => 'fas fa-fw fa-chalkboard-teacher',
            ],
            [
                'text' => 'Students',
                'url' => 'admin/students',
                'icon' => 'fas fa-fw fa-user-graduate',
            ],

            ['header' => 'COURSE MANAGEMENT'],
            [
                'text' => 'Courses',
                'url' => 'admin/courses',
                'icon' => 'fas fa-fw fa-book',
            ],
            [
                'text' => 'Lessons',
                'url' => 'admin/lessons',
                'icon' => 'fas fa-fw fa-list',
            ],
            [
                'text' => 'Course Dates',
                'url' => 'admin/course-dates',
                'icon' => 'fas fa-fw fa-calendar-alt',
            ],

            ['header' => 'BUSINESS'],
            [
                'text' => 'Orders',
                'url' => 'admin/orders',
                'icon' => 'fas fa-fw fa-shopping-cart',
            ],
            [
                'text' => 'Support',
                'url' => 'admin/support',
                'icon' => 'fas fa-fw fa-life-ring',
            ],
            [
                'text' => 'Reports',
                'url' => 'admin/reports',
                'icon' => 'fas fa-fw fa-chart-bar',
            ],
        ];

        // Get custom menu from database if available, otherwise use default
        $customMenu = $this->settingHelper->get('adminlte.menu', null);

        return [
            'menu' => $customMenu ? json_decode($customMenu, true) : $defaultMenu,
        ];
    }

    // Menu Filters Configuration
    public function getMenuFilters()
    {
        $defaultFilters = [
            'JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter',
            'JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter',
            'JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter',
            'JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter',
            'JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter',
            'JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter',
            'JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter',
        ];

        $customFilters = $this->settingHelper->get('adminlte.menu_filters', null);

        return [
            'filters' => $customFilters ? json_decode($customFilters, true) : $defaultFilters,
        ];
    }

    // Plugins Configuration
    public function getPluginsConfig()
    {
        $defaultPlugins = [
            'Datatables' => [
                'active' => false,
                'files' => [
                    [
                        'type' => 'js',
                        'asset' => false,
                        'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                    ],
                    [
                        'type' => 'js',
                        'asset' => false,
                        'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                    ],
                    [
                        'type' => 'css',
                        'asset' => false,
                        'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                    ],
                ],
            ],
            'Select2' => [
                'active' => false,
                'files' => [
                    [
                        'type' => 'js',
                        'asset' => false,
                        'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                    ],
                    [
                        'type' => 'css',
                        'asset' => false,
                        'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                    ],
                ],
            ],
            'Chartjs' => [
                'active' => false,
                'files' => [
                    [
                        'type' => 'js',
                        'asset' => false,
                        'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                    ],
                ],
            ],
            'Sweetalert2' => [
                'active' => false,
                'files' => [
                    [
                        'type' => 'js',
                        'asset' => false,
                        'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8',
                    ],
                ],
            ],
            'Pace' => [
                'active' => false,
                'files' => [
                    [
                        'type' => 'css',
                        'asset' => false,
                        'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                    ],
                    [
                        'type' => 'js',
                        'asset' => false,
                        'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                    ],
                ],
            ],
        ];

        $customPlugins = $this->settingHelper->get('adminlte.plugins', null);

        return [
            'plugins' => $customPlugins ? json_decode($customPlugins, true) : $defaultPlugins,
        ];
    }

    // Individual Plugin Getters
    public function getDatatablesPlugin()
    {
        return [
            'plugins' => [
                'Datatables' => [
                    'active' => $this->settingHelper->get('adminlte.plugins_datatables_active', false),
                ],
            ],
        ];
    }

    public function getSelect2Plugin()
    {
        return [
            'plugins' => [
                'Select2' => [
                    'active' => $this->settingHelper->get('adminlte.plugins_select2_active', false),
                ],
            ],
        ];
    }

    public function getChartjsPlugin()
    {
        return [
            'plugins' => [
                'Chartjs' => [
                    'active' => $this->settingHelper->get('adminlte.plugins_chartjs_active', false),
                ],
            ],
        ];
    }

    public function getSweetalert2Plugin()
    {
        return [
            'plugins' => [
                'Sweetalert2' => [
                    'active' => $this->settingHelper->get('adminlte.plugins_sweetalert2_active', false),
                ],
            ],
        ];
    }

    public function getPacePlugin()
    {
        return [
            'plugins' => [
                'Pace' => [
                    'active' => $this->settingHelper->get('adminlte.plugins_pace_active', false),
                ],
            ],
        ];
    }

    // Master method to get all configuration at once
    public function getAllConfiguration()
    {
        return array_merge(
            $this->getTitle(),
            $this->getTitlePrefix(),
            $this->getTitlePostfix(),
            $this->getUseIcoOnly(),
            $this->getUseFullFavicon(),
            $this->getGoogleFonts(),
            $this->getLogo(),
            $this->getLogoImg(),
            $this->getLogoImgClass(),
            $this->getLogoImgXl(),
            $this->getLogoImgXlClass(),
            $this->getLogoImgAlt(),
            $this->getAuthLogo(),
            $this->getPreloader(),
            $this->getUsermenuEnabled(),
            $this->getUsermenuHeader(),
            $this->getUsermenuHeaderClass(),
            $this->getUsermenuImage(),
            $this->getUsermenuDesc(),
            $this->getUsermenuProfileUrl(),
            $this->getLayoutTopnav(),
            $this->getLayoutBoxed(),
            $this->getLayoutFixedSidebar(),
            $this->getLayoutFixedNavbar(),
            $this->getLayoutFixedFooter(),
            $this->getLayoutDarkMode(),
            $this->getClassesAuthCard(),
            $this->getClassesAuthHeader(),
            $this->getClassesAuthBody(),
            $this->getClassesAuthFooter(),
            $this->getClassesAuthIcon(),
            $this->getClassesAuthBtn(),
            $this->getClassesBody(),
            $this->getClassesBrand(),
            $this->getClassesBrandText(),
            $this->getClassesContentWrapper(),
            $this->getClassesContentHeader(),
            $this->getClassesContent(),
            $this->getClassesSidebar(),
            $this->getClassesSidebarNav(),
            $this->getClassesTopnav(),
            $this->getClassesTopnavNav(),
            $this->getClassesTopnavContainer(),
            $this->getSidebarMini(),
            $this->getSidebarCollapse(),
            $this->getSidebarCollapseAutoSize(),
            $this->getSidebarCollapseRemember(),
            $this->getSidebarCollapseRememberNoTransition(),
            $this->getSidebarScrollbarTheme(),
            $this->getSidebarScrollbarAutoHide(),
            $this->getSidebarNavAccordion(),
            $this->getSidebarNavAnimationSpeed(),
            $this->getRightSidebar(),
            $this->getRightSidebarIcon(),
            $this->getRightSidebarTheme(),
            $this->getRightSidebarSlide(),
            $this->getRightSidebarPush(),
            $this->getRightSidebarScrollbarTheme(),
            $this->getRightSidebarScrollbarAutoHide(),
            $this->getRightSidebarWidth(),
            $this->getUseRouteUrl(),
            $this->getDashboardUrl(),
            $this->getLogoutUrl(),
            $this->getLoginUrl(),
            $this->getRegisterUrl(),
            $this->getPasswordResetUrl(),
            $this->getPasswordEmailUrl(),
            $this->getProfileUrl(),
            $this->getDisableDarkmodeRoutes(),
            $this->getLaravelAssetBundling(),
            $this->getIframeConfig(),
            $this->getLivewire(),
            $this->getMenuConfig(),
            $this->getMenuFilters(),
            $this->getPluginsConfig()
        );
    }

    // Get configuration for specific sections
    public function getTitleConfiguration()
    {
        return array_merge(
            $this->getTitle(),
            $this->getTitlePrefix(),
            $this->getTitlePostfix()
        );
    }

    public function getLogoConfiguration()
    {
        return array_merge(
            $this->getLogo(),
            $this->getLogoImg(),
            $this->getLogoImgClass(),
            $this->getLogoImgXl(),
            $this->getLogoImgXlClass(),
            $this->getLogoImgAlt(),
            $this->getAuthLogo()
        );
    }

    public function getLayoutConfiguration()
    {
        return array_merge(
            $this->getLayoutTopnav(),
            $this->getLayoutBoxed(),
            $this->getLayoutFixedSidebar(),
            $this->getLayoutFixedNavbar(),
            $this->getLayoutFixedFooter(),
            $this->getLayoutDarkMode()
        );
    }

    public function getClassesConfiguration()
    {
        return array_merge(
            $this->getClassesAuthCard(),
            $this->getClassesAuthHeader(),
            $this->getClassesAuthBody(),
            $this->getClassesAuthFooter(),
            $this->getClassesAuthIcon(),
            $this->getClassesAuthBtn(),
            $this->getClassesBody(),
            $this->getClassesBrand(),
            $this->getClassesBrandText(),
            $this->getClassesContentWrapper(),
            $this->getClassesContentHeader(),
            $this->getClassesContent(),
            $this->getClassesSidebar(),
            $this->getClassesSidebarNav(),
            $this->getClassesTopnav(),
            $this->getClassesTopnavNav(),
            $this->getClassesTopnavContainer()
        );
    }

    public function getSidebarConfiguration()
    {
        return array_merge(
            $this->getSidebarMini(),
            $this->getSidebarCollapse(),
            $this->getSidebarCollapseAutoSize(),
            $this->getSidebarCollapseRemember(),
            $this->getSidebarCollapseRememberNoTransition(),
            $this->getSidebarScrollbarTheme(),
            $this->getSidebarScrollbarAutoHide(),
            $this->getSidebarNavAccordion(),
            $this->getSidebarNavAnimationSpeed(),
            $this->getRightSidebar(),
            $this->getRightSidebarIcon(),
            $this->getRightSidebarTheme(),
            $this->getRightSidebarSlide(),
            $this->getRightSidebarPush(),
            $this->getRightSidebarScrollbarTheme(),
            $this->getRightSidebarScrollbarAutoHide(),
            $this->getRightSidebarWidth()
        );
    }

    public function getUrlConfiguration()
    {
        return array_merge(
            $this->getUseRouteUrl(),
            $this->getDashboardUrl(),
            $this->getLogoutUrl(),
            $this->getLoginUrl(),
            $this->getRegisterUrl(),
            $this->getPasswordResetUrl(),
            $this->getPasswordEmailUrl(),
            $this->getProfileUrl(),
            $this->getDisableDarkmodeRoutes()
        );
    }
}
