/**
 * Frost Frontend Site JavaScript
 * Professional frontend functionality for the site layout
 */

// DOM Ready
document.addEventListener("DOMContentLoaded", function () {
    console.log("Frost Frontend Site JavaScript Loaded");

    // Initialize all modules
    initLoader();
    initScrollToTop();
    initSmoothScrolling();
    initMobileMenu();
    initFormEnhancements();
    initTooltips();
});

/**
 * Professional Page Loader
 */
function initLoader() {
    const preloader = document.getElementById("preloaders");
    if (!preloader) return;

    // Show loader initially
    preloader.style.display = "flex";

    // Hide loader when page is fully loaded
    window.addEventListener("load", function () {
        // Add loaded class for animation
        preloader.classList.add("loaded");

        // Remove from DOM after transition
        setTimeout(() => {
            preloader.style.display = "none";
        }, 500);
    });

    // Fallback - hide loader after 3 seconds maximum
    setTimeout(() => {
        if (preloader.style.display !== "none") {
            preloader.classList.add("loaded");
            setTimeout(() => {
                preloader.style.display = "none";
            }, 500);
        }
    }, 3000);
}

/**
 * Scroll to Top Button
 */
function initScrollToTop() {
    const scrollUpBtn = document.querySelector(".scrollUp");
    if (!scrollUpBtn) return;

    // Show/hide based on scroll position
    window.addEventListener("scroll", function () {
        if (window.pageYOffset > 100) {
            scrollUpBtn.classList.add("show");
        } else {
            scrollUpBtn.classList.remove("show");
        }
    });

    // Smooth scroll to top
    scrollUpBtn.addEventListener("click", function (e) {
        e.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: "smooth",
        });
    });
}

/**
 * Smooth Scrolling for Anchor Links
 */
function initSmoothScrolling() {
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener("click", function (e) {
            const href = this.getAttribute("href");
            if (href === "#") return;

            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({
                    behavior: "smooth",
                    block: "start",
                });
            }
        });
    });
}

/**
 * Mobile Menu Toggle
 */
function initMobileMenu() {
    const mobileToggle = document.querySelector(".topbar-mobile-toggle");
    const mobileMenu = document.querySelector("#navbarNav");

    if (!mobileToggle || !mobileMenu) return;

    mobileToggle.addEventListener("click", function () {
        const isExpanded = this.getAttribute("aria-expanded") === "true";
        this.setAttribute("aria-expanded", !isExpanded);

        // Toggle animation classes
        if (mobileMenu.classList.contains("show")) {
            mobileMenu.classList.remove("show");
        } else {
            mobileMenu.classList.add("show");
        }
    });

    // Close mobile menu when clicking outside
    document.addEventListener("click", function (e) {
        if (
            !mobileToggle.contains(e.target) &&
            !mobileMenu.contains(e.target)
        ) {
            mobileMenu.classList.remove("show");
            mobileToggle.setAttribute("aria-expanded", "false");
        }
    });

    // Close mobile menu on window resize to desktop
    window.addEventListener("resize", function () {
        if (window.innerWidth >= 992) {
            // Bootstrap lg breakpoint
            mobileMenu.classList.remove("show");
            mobileToggle.setAttribute("aria-expanded", "false");
        }
    });
}

/**
 * Form Field Enhancements
 */
function initFormEnhancements() {
    // Floating label effects
    document.querySelectorAll(".form-control").forEach((input) => {
        // Focus effects
        input.addEventListener("focus", function () {
            this.closest(".form-group, .form-floating")?.classList.add(
                "focused"
            );
        });

        input.addEventListener("blur", function () {
            this.closest(".form-group, .form-floating")?.classList.remove(
                "focused"
            );
        });

        // Input validation visual feedback
        input.addEventListener("input", function () {
            if (this.validity.valid) {
                this.classList.remove("is-invalid");
                this.classList.add("is-valid");
            } else {
                this.classList.remove("is-valid");
                this.classList.add("is-invalid");
            }
        });
    });

    // Password visibility toggle
    document.querySelectorAll("[data-toggle-password]").forEach((toggle) => {
        toggle.addEventListener("click", function () {
            const target = document.querySelector(
                this.getAttribute("data-toggle-password")
            );
            if (target) {
                const type =
                    target.getAttribute("type") === "password"
                        ? "text"
                        : "password";
                target.setAttribute("type", type);

                // Update icon
                const icon = this.querySelector("i");
                if (icon) {
                    icon.classList.toggle("fa-eye");
                    icon.classList.toggle("fa-eye-slash");
                }
            }
        });
    });
}

/**
 * Bootstrap Tooltips Initialization
 */
function initTooltips() {
    // Initialize Bootstrap tooltips if available
    if (typeof bootstrap !== "undefined" && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(
            document.querySelectorAll(
                '[data-bs-toggle="tooltip"], [data-tooltip]'
            )
        );
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                placement:
                    tooltipTriggerEl.getAttribute("data-placement") || "top",
            });
        });
    }
}

/**
 * Utility Functions
 */
window.FrostSite = {
    // Show notification
    showNotification: function (message, type = "info") {
        console.log(`[${type.toUpperCase()}] ${message}`);
        // You can integrate with your notification system here
    },

    // Smooth scroll to element
    scrollToElement: function (selector, offset = 0) {
        const element = document.querySelector(selector);
        if (element) {
            const elementPosition = element.getBoundingClientRect().top;
            const offsetPosition =
                elementPosition + window.pageYOffset - offset;

            window.scrollTo({
                top: offsetPosition,
                behavior: "smooth",
            });
        }
    },

    // Toggle loading state
    setLoading: function (element, isLoading = true) {
        if (typeof element === "string") {
            element = document.querySelector(element);
        }

        if (element) {
            if (isLoading) {
                element.classList.add("loading");
                element.disabled = true;
            } else {
                element.classList.remove("loading");
                element.disabled = false;
            }
        }
    },
};

// Export for module systems
if (typeof module !== "undefined" && module.exports) {
    module.exports = window.FrostSite;
}
