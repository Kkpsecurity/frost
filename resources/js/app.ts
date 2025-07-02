/**
 * Load the React components for the Student Portal
 * Only when the url is /classroom/portal
 */
if (
    window.location.pathname.split("/")[1] === "classroom" &&
    window.location.pathname.split("/")[2] === "portal"
) {
    require("./React/Pages/Web/Students/StudentPortal");
}

if (
    window.location.pathname.split("/")[1] === "classroom" &&
    window.location.pathname.split("/")[2] === "portal" &&
    window.location.pathname.split("/")[3] === "zoom"
) {
    require("./React/Pages/Web/Students/Zoom/ScreenSharePlayer");
}

/**
 * Load the React components for the account profile dashboard
 * Only when the url is /account/profile
if (window.location.pathname.split("/")[1] === "account") {
    require("./React/Pages/Web/Account/ProfileDashboard");
}
 */
