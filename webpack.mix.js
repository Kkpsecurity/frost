/**
 * |--------------------------------------------------------------------------
 *  | Florida Online Asset Managemnt
 *  | @version 1.0.3
 *  | @lastUpdated 2023-11-07
 *  |--------------------------------------------------------------------------
 *  |
 *
 * @format
 */

const publicPath = "./public/assets";
const adminPath = publicPath + "/admin";

const mix = require("laravel-mix");
let rmdir = require("rmdir");
const fs = require("fs").promises;

let resetAllAssets = true;

if (resetAllAssets === true) {
  fs.rm(publicPath, { recursive: true, force: true })
    .then(() => console.log("Assets directory removed successfully."))
    .catch((err) => console.error("Error removing assets directory:", err));
}

/**
 * React Scripts
 */
mix.ts("resources/js/app.ts", "public/js").react();
mix.ts("resources/js/admin.ts", "public/js").react();

/*********************************************************
 *  F R O N T  E N D
 */
const theme_root = "./resources/themes/frost";
const theme = theme_root + "/bultifore";

// mix.copyDirectory(theme + "/css", publicPath + "/css");

/**
 * Copy Assets and fonts
 */
if (resetAllAssets === true) {
  mix.copyDirectory(theme + "/fonts", publicPath + "/fonts");
  mix.copyDirectory(theme + "/img", publicPath + "/img");
  mix.copyDirectory(theme_root + "/vendor", publicPath + "/vendor");
  mix.copyDirectory("./resources/assets", publicPath);
}

mix.copy(
  "resources/css/frostplayer.css",
  publicPath + "/css/frostplayer.css"
);

mix.copy(
  "resources/js/site/frostplayer.js",
  publicPath + "/js/frostplayer.js"
);


mix.styles(
  [
    theme + "/css/animate.css",
    theme + "/css/bootstrap.min.css",
    theme + "/css/owl.carousel.css",
    theme + "/css/owl.transitions.css",
    theme + "/css/nice-select.css",
    theme + "/css/meanmenu.min.css",
    theme + "/css/themify-icons.css",
    theme + "/css/flaticon.css",
    theme + "/css/magnific.min.css",
  ],
  publicPath + "/css/vendor.css"
);

mix.styles(["resources/css/site/site.css"], publicPath + "/css/site.css");

mix.styles(
  [
    theme + "/style.css",
    theme + "/css/root.css",
    theme + "/css/html.css",
    theme + "/css/header.css",
    theme + "/css/breadcrumbs.css",
    theme + "/css/account-dashboard.css",
    theme + "/css/slider-login.css",
    theme + "/css/login-register.css",
    theme + "/css/product-feature.css",
    theme + "/css/getting-started.css",
    theme + "/css/schedule.css",
    theme + "/css/support.css",
    theme + "/css/contactus.css",
    theme + "/css/blog.css",
    theme + "/css/table.css",
    theme + "/css/about.css",
    theme + "/css/footer.css",
  ],
  publicPath + "/css/theme.css"
);

/**** SCRIPTS
 *  theme + "/js/magnific.min.js",
 * ***/
mix.js(
  [
    theme + "/js/bootstrap.min.js",
    theme + "/js/wow.min.js",
    theme + "/js/owl.carousel.min.js",
    theme + "/js/jquery.stellar.min.js",
    theme + "/js/jquery.meanmenu.js",
    theme + "/js/jquery.nice-select.min.js",
  ],
  publicPath + "/js/vendor.js"
);

mix.js(
  [theme + "/js/plugins.js", theme + "/js/main.js"],
  publicPath + "/js/theme.js"
);

mix.js(
  [
    "resources/js/vendor.js",
    "resources/js/site/laravel-compat.js",
    "resources/js/site/ExitFullscreenListener.js",
    "resources/js/site/PayFlowProForm.js",
    "resources/js/site/reloadr.js",
    "resources/js/site/site.js",
  ],
  publicPath + "/js/site.js"
);

// #############################################>

/**
 * Admin Assets
 */

mix.copyDirectory("resources/themes/admin", publicPath + "/admin");
mix.copy(
  "resources/js/site/FrostCharts.js",
  publicPath + "/admin/js/FrostCharts.js"
);

if (mix.inProduction()) {
  mix.version();
} else {
  mix.sourceMaps();
}
