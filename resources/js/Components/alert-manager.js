/**
 * Alert Manager
 * Centralized alert and notification management system
 */

class AlertManager {
    constructor() {
        this.alerts = new Map();
        this.defaultOptions = {
            duration: 5000,
            closable: true,
            position: 'top-right',
            animation: 'fade',
            showIcon: true,
        };
        this.initialize();
    }

    initialize() {
        // Create alert container if it doesn't exist
        if (!document.getElementById('alert-container')) {
            const container = document.createElement('div');
            container.id = 'alert-container';
            container.className = 'alert-container position-fixed';
            container.style.cssText = `
                top: 20px;
                right: 20px;
                z-index: 9999;
                max-width: 400px;
                pointer-events: none;
            `;
            document.body.appendChild(container);
        }

        // Listen for custom alert events
        document.addEventListener('showAlert', (event) => {
            this.show(event.detail);
        });

        document.addEventListener('hideAlert', (event) => {
            this.hide(event.detail.id);
        });

        // Initialize existing alerts on page load
        this.initializeExistingAlerts();
    }

    initializeExistingAlerts() {
        const existingAlerts = document.querySelectorAll('.alert[data-alert-id]');
        existingAlerts.forEach(alert => {
            const id = alert.getAttribute('data-alert-id');
            const autoDismiss = alert.hasAttribute('data-auto-dismiss');
            const duration = parseInt(alert.getAttribute('data-duration')) || this.defaultOptions.duration;

            if (autoDismiss) {
                setTimeout(() => this.hide(id), duration);
            }

            // Add close button functionality
            const closeBtn = alert.querySelector('.btn-close, [data-bs-dismiss="alert"]');
            if (closeBtn) {
                closeBtn.addEventListener('click', () => this.hide(id));
            }
        });
    }

    show(options) {
        const config = { ...this.defaultOptions, ...options };
        const id = config.id || this.generateId();

        // Create alert element
        const alertElement = this.createAlertElement(id, config);
        
        // Add to container
        const container = document.getElementById('alert-container');
        container.appendChild(alertElement);

        // Store reference
        this.alerts.set(id, {
            element: alertElement,
            config: config,
            timeout: null
        });

        // Animate in
        requestAnimationFrame(() => {
            alertElement.style.opacity = '1';
            alertElement.style.transform = 'translateX(0)';
        });

        // Auto-dismiss if specified
        if (config.duration && config.duration > 0) {
            const timeout = setTimeout(() => this.hide(id), config.duration);
            this.alerts.get(id).timeout = timeout;
        }

        // Emit event
        this.emit('alertShown', { id, config });

        return id;
    }

    hide(id) {
        const alert = this.alerts.get(id);
        if (!alert) return;

        // Clear timeout
        if (alert.timeout) {
            clearTimeout(alert.timeout);
        }

        // Animate out
        alert.element.style.opacity = '0';
        alert.element.style.transform = 'translateX(100%)';

        // Remove after animation
        setTimeout(() => {
            if (alert.element.parentNode) {
                alert.element.parentNode.removeChild(alert.element);
            }
            this.alerts.delete(id);
            this.emit('alertHidden', { id });
        }, 300);
    }

    hideAll() {
        this.alerts.forEach((_, id) => this.hide(id));
    }

    createAlertElement(id, config) {
        const alert = document.createElement('div');
        alert.id = `alert-${id}`;
        alert.setAttribute('data-alert-id', id);
        alert.className = `alert alert-${config.type || 'info'} alert-dismissible mb-2`;
        alert.style.cssText = `
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease;
            pointer-events: auto;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border: none;
        `;

        let iconHtml = '';
        if (config.showIcon) {
            const iconMap = {
                success: 'fas fa-check-circle',
                error: 'fas fa-exclamation-circle',
                warning: 'fas fa-exclamation-triangle',
                info: 'fas fa-info-circle'
            };
            const iconClass = iconMap[config.type] || iconMap.info;
            iconHtml = `<i class="${iconClass} me-2"></i>`;
        }

        const closeButton = config.closable ? 
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' : '';

        alert.innerHTML = `
            ${iconHtml}
            <span class="alert-message">${config.message || ''}</span>
            ${closeButton}
        `;

        // Add close functionality
        if (config.closable) {
            const closeBtn = alert.querySelector('.btn-close');
            closeBtn.addEventListener('click', () => this.hide(id));
        }

        return alert;
    }

    generateId() {
        return 'alert_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }

    emit(eventName, data) {
        const event = new CustomEvent(eventName, { detail: data });
        document.dispatchEvent(event);
    }

    // Convenience methods
    success(message, options = {}) {
        return this.show({ ...options, type: 'success', message });
    }

    error(message, options = {}) {
        return this.show({ ...options, type: 'danger', message });
    }

    warning(message, options = {}) {
        return this.show({ ...options, type: 'warning', message });
    }

    info(message, options = {}) {
        return this.show({ ...options, type: 'info', message });
    }

    // Laravel integration helpers
    showLaravelErrors(errors) {
        if (typeof errors === 'object') {
            Object.keys(errors).forEach(field => {
                const fieldErrors = Array.isArray(errors[field]) ? errors[field] : [errors[field]];
                fieldErrors.forEach(error => {
                    this.error(`${field}: ${error}`);
                });
            });
        } else if (typeof errors === 'string') {
            this.error(errors);
        }
    }

    showFlashMessages() {
        // Check for Laravel flash messages
        const flashSuccess = document.querySelector('meta[name="flash-success"]');
        const flashError = document.querySelector('meta[name="flash-error"]');
        const flashWarning = document.querySelector('meta[name="flash-warning"]');
        const flashInfo = document.querySelector('meta[name="flash-info"]');

        if (flashSuccess) {
            this.success(flashSuccess.getAttribute('content'));
        }
        if (flashError) {
            this.error(flashError.getAttribute('content'));
        }
        if (flashWarning) {
            this.warning(flashWarning.getAttribute('content'));
        }
        if (flashInfo) {
            this.info(flashInfo.getAttribute('content'));
        }
    }
}

// Create global instance
const alertManager = new AlertManager();

// Global functions for easy access
window.showAlert = (options) => alertManager.show(options);
window.hideAlert = (id) => alertManager.hide(id);
window.showSuccess = (message, options) => alertManager.success(message, options);
window.showError = (message, options) => alertManager.error(message, options);
window.showWarning = (message, options) => alertManager.warning(message, options);
window.showInfo = (message, options) => alertManager.info(message, options);

// Initialize flash messages on page load
document.addEventListener('DOMContentLoaded', () => {
    alertManager.showFlashMessages();
});

// jQuery integration if available
if (typeof jQuery !== 'undefined') {
    jQuery.extend({
        showAlert: (options) => alertManager.show(options),
        showSuccess: (message, options) => alertManager.success(message, options),
        showError: (message, options) => alertManager.error(message, options),
        showWarning: (message, options) => alertManager.warning(message, options),
        showInfo: (message, options) => alertManager.info(message, options),
    });
}

export default alertManager;
