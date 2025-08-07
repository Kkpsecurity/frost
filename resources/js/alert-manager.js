/**
 * Alert Management Utility
 * Provides consistent alert handling across the application
 */

window.AlertManager = {
    /**
     * Default timeout for temporary alerts (in milliseconds)
     */
    defaultTimeout: 5000,

    /**
     * Initialize auto-hide behavior for alerts
     * Call this function on page load to set up automatic alert hiding
     */
    init: function() {
        this.setupAutoHide();
    },

    /**
     * Set up auto-hide for temporary alerts only
     * Excludes persistent alerts like "no content found" messages
     */
    setupAutoHide: function() {
        // Only auto-hide alerts that:
        // 1. Have the 'alert-temporary' class, OR
        // 2. Are success/warning/danger alerts but NOT info alerts, AND
        // 3. Don't have the 'no-auto-hide' class
        const autoHideSelector = '.alert.alert-temporary, ' +
                               '.alert.alert-success:not(.no-auto-hide), ' +
                               '.alert.alert-warning:not(.no-auto-hide), ' +
                               '.alert.alert-danger:not(.no-auto-hide)';

        $(autoHideSelector).each(function() {
            const $alert = $(this);

            // Don't auto-hide if it has persistent class or no-auto-hide class
            if ($alert.hasClass('alert-persistent') || $alert.hasClass('no-auto-hide')) {
                return;
            }

            // Auto-hide after default timeout
            setTimeout(() => {
                $alert.fadeOut(300, function() {
                    $(this).remove();
                });
            }, AlertManager.defaultTimeout);
        });
    },

    /**
     * Show a temporary notification that will auto-hide
     * @param {string} type - 'success', 'warning', 'danger', or 'info'
     * @param {string} message - The message to display
     * @param {number} timeout - Optional timeout in milliseconds (default: 5000)
     */
    showTemporary: function(type, message, timeout = null) {
        timeout = timeout || this.defaultTimeout;

        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show alert-temporary" role="alert">
                <i class="fas fa-${this.getIconForType(type)} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        const $alert = $(alertHtml);

        // Add to notification container
        this.getNotificationContainer().prepend($alert);

        // Auto-hide after timeout
        setTimeout(() => {
            $alert.fadeOut(300, function() {
                $(this).remove();
            });
        }, timeout);

        return $alert;
    },

    /**
     * Show a persistent notification that stays visible
     * @param {string} type - 'success', 'warning', 'danger', or 'info'
     * @param {string} message - The message to display
     */
    showPersistent: function(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible alert-persistent no-auto-hide" role="alert">
                <i class="fas fa-${this.getIconForType(type)} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        const $alert = $(alertHtml);

        // Add to notification container
        this.getNotificationContainer().prepend($alert);

        return $alert;
    },

    /**
     * Get or create notification container
     */
    getNotificationContainer: function() {
        let $container = $('#global-notification-container');
        if ($container.length === 0) {
            $container = $('<div id="global-notification-container" class="position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;"></div>');
            $('body').append($container);
        }
        return $container;
    },

    /**
     * Get Font Awesome icon class for alert type
     */
    getIconForType: function(type) {
        const icons = {
            'success': 'check-circle',
            'warning': 'exclamation-triangle',
            'danger': 'exclamation-circle',
            'info': 'info-circle'
        };
        return icons[type] || 'info-circle';
    }
};

// Auto-initialize when DOM is ready
$(document).ready(function() {
    AlertManager.init();
});
