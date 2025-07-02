/**
 * First we will load all of this project's JavaScript dependencies which
 * includes React and other helpers. It's a great starting point while
 * building robust, powerful web applications using React + Laravel.
 */

if (
    window.location.pathname.split("/")[1] === "admin" &&
    window.location.pathname.split("/")[2] === "instructors"
) {
    require("./React/Pages/Admin/Instructors/InstructorPortal");
}

if (
    window.location.pathname.split("/")[1] === "admin" &&
    window.location.pathname.split("/")[2] === "frost-support"
) {
    require("./React/Pages/Admin/Support/Dashboard");
}
