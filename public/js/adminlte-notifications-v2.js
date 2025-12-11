/**
 * Frost AdminLTE Notifications & Messages Integration
 * Works with AdminLTE navbar notification widgets
 */
class FrostAdminLTENotifications {
    constructor() {
        this.notifications = [];
        this.messages = [];
        this.refreshInterval = 30000; // 30 seconds
        this.init();
    }

    async init() {
        // Wait for jQuery and AdminLTE to load
        if (typeof $ === 'undefined') {
            setTimeout(() => this.init(), 100);
            return;
        }

        // Ensure notification icons exist in navbar
        this.ensureNotificationIcons();

        this.bindEvents();
        await this.loadNotifications();
        await this.loadMessages();
        this.updateBadges();

        console.log('✅ Frost AdminLTE Notifications initialized');

        // Auto-refresh
        setInterval(() => {
            this.loadNotifications();
            this.loadMessages();
        }, this.refreshInterval);
    }

    ensureNotificationIcons() {
        const navbar = $('.navbar-nav.ml-auto, .navbar-nav:last-child');

        // Check if notification icons already exist
        if ($('#notifications-toggle').length === 0) {
            console.log('🔔 Adding notification bell to navbar');
            // Add notification bell
            navbar.prepend(`
                <li class="nav-item dropdown">
                    <a class="nav-link" href="#" id="notifications-toggle" data-toggle="dropdown">
                        <i class="far fa-bell"></i>
                        <span class="badge badge-warning navbar-badge" style="display: none;">0</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right"></div>
                </li>
            `);
        } else {
            console.log('🔔 Notification bell already exists');
        }

        if ($('#messages-toggle').length === 0) {
            console.log('📧 Adding message envelope to navbar');
            // Add message envelope
            navbar.prepend(`
                <li class="nav-item dropdown">
                    <a class="nav-link" href="#" id="messages-toggle" data-toggle="dropdown">
                        <i class="far fa-envelope"></i>
                        <span class="badge badge-danger navbar-badge" style="display: none;">0</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right"></div>
                </li>
            `);
        } else {
            console.log('📧 Message envelope already exists');
        }
    }

    bindEvents() {
        // Handle AdminLTE notification dropdown clicks
        $(document).on('click', '#notifications-toggle', (e) => {
            e.preventDefault();
            this.onNotificationsDropdownOpen();
        });

        $(document).on('click', '#messages-toggle', (e) => {
            e.preventDefault();
            this.onMessagesDropdownOpen();
        });

        // Handle notification item clicks
        $(document).on('click', '.notification-item', (e) => {
            const notificationId = $(e.currentTarget).data('notification-id');
            this.handleNotificationClick(notificationId);
        });

        // Handle message item clicks
        $(document).on('click', '.message-item', (e) => {
            const threadId = $(e.currentTarget).data('thread-id');
            this.handleMessageClick(threadId);
        });

        // Handle mark all as read
        $(document).on('click', '.mark-all-read', async (e) => {
            e.preventDefault();
            await this.markAllNotificationsRead();
        });
    }

    async loadNotifications() {
        try {
            const response = await fetch('/messaging/notifications', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                this.notifications = await response.json();
                this.updateNotificationsBadge();
                this.updateNotificationsDropdown();
            } else {
                console.warn('Failed to load notifications:', response.status);
            }
        } catch (error) {
            console.error('Failed to load notifications:', error);
        }
    }

    async loadMessages() {
        try {
            const response = await fetch('/messaging/threads', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                this.messages = await response.json();
                this.updateMessagesBadge();
                this.updateMessagesDropdown();
            } else {
                console.warn('Failed to load messages:', response.status);
            }
        } catch (error) {
            console.error('Failed to load messages:', error);
        }
    }

    updateNotificationsBadge() {
        const unreadCount = this.notifications.filter(n => !n.read_at).length;
        const badge = $('#notifications-toggle .badge');

        if (unreadCount > 0) {
            badge.text(unreadCount).show();
        } else {
            badge.hide();
        }

        console.log('🔔 Notifications badge updated:', unreadCount);
    }

    updateMessagesBadge() {
        const unreadCount = this.messages.filter(m => m.unread_count > 0).length;
        const badge = $('#messages-toggle .badge');

        if (unreadCount > 0) {
            badge.text(unreadCount).show();
        } else {
            badge.hide();
        }

        console.log('📧 Messages badge updated:', unreadCount);
    }

    updateNotificationsDropdown() {
        const dropdown = $('#notifications-toggle').next('.dropdown-menu');
        const html = this.renderNotificationsList();
        dropdown.html(html);
    }

    updateMessagesDropdown() {
        const dropdown = $('#messages-toggle').next('.dropdown-menu');
        const html = this.renderMessagesList();
        dropdown.html(html);
    }

    renderNotificationsList() {
        if (this.notifications.length === 0) {
            return `
                <span class="dropdown-item dropdown-header">No notifications</span>
                <div class="dropdown-item text-center text-muted py-3">
                    <i class="far fa-bell fa-2x"></i><br>
                    No new notifications
                </div>
            `;
        }

        let html = `<span class="dropdown-item dropdown-header">${this.notifications.length} Notifications</span>`;

        this.notifications.slice(0, 5).forEach(notification => {
            const isRead = notification.read_at;
            const textClass = isRead ? 'text-muted' : '';

            html += `
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item notification-item ${textClass}"
                   data-notification-id="${notification.id}">
                    <i class="far fa-bell mr-2"></i> ${notification.title || 'Notification'}
                    <span class="float-right text-muted text-sm">${this.formatTime(notification.created_at)}</span>
                    <p class="text-sm mb-0">${notification.message}</p>
                </a>
            `;
        });

        html += `
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item dropdown-footer mark-all-read">Mark all as read</a>
            <a href="/messaging/notifications" class="dropdown-item dropdown-footer">View All Notifications</a>
        `;

        return html;
    }

    renderMessagesList() {
        if (this.messages.length === 0) {
            return `
                <span class="dropdown-item dropdown-header">No messages</span>
                <div class="dropdown-item text-center text-muted py-3">
                    <i class="far fa-envelope fa-2x"></i><br>
                    No new messages
                </div>
            `;
        }

        let html = `<span class="dropdown-item dropdown-header">${this.messages.length} Messages</span>`;

        this.messages.slice(0, 5).forEach(thread => {
            const hasUnread = thread.unread_count > 0;
            const textClass = hasUnread ? 'font-weight-bold' : 'text-muted';

            html += `
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item message-item" data-thread-id="${thread.id}">
                    <div class="media">
                        <div class="media-object bg-primary rounded-circle d-flex align-items-center justify-content-center mr-3"
                             style="width: 40px; height: 40px;">
                            <i class="far fa-user text-white"></i>
                        </div>
                        <div class="media-body">
                            <h3 class="dropdown-item-title ${textClass}">
                                ${thread.title}
                                ${hasUnread ? `<span class="float-right text-sm text-danger"><i class="fas fa-circle"></i></span>` : ''}
                            </h3>
                            <p class="text-sm">${thread.last_message || 'No messages yet'}</p>
                            <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> ${this.formatTime(thread.updated_at)}</p>
                        </div>
                    </div>
                </a>
            `;
        });

        html += `
            <div class="dropdown-divider"></div>
            <a href="/messaging" class="dropdown-item dropdown-footer">See All Messages</a>
        `;

        return html;
    }

    onNotificationsDropdownOpen() {
        // Update notifications when dropdown is opened
        this.loadNotifications();
    }

    onMessagesDropdownOpen() {
        // Update messages when dropdown is opened
        this.loadMessages();
    }

    async handleNotificationClick(notificationId) {
        try {
            // Mark notification as read
            await fetch(`/messaging/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Refresh notifications
            await this.loadNotifications();

        } catch (error) {
            console.error('Failed to mark notification as read:', error);
        }
    }

    async markAllNotificationsRead() {
        try {
            const response = await fetch('/messaging/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            if (response.ok) {
                await this.loadNotifications();
            }
        } catch (error) {
            console.error('Failed to mark notifications as read:', error);
        }
    }

    handleMessageClick(threadId) {
        // Navigate to message thread
        window.location.href = `/messaging/thread/${threadId}`;
    }

    formatTime(timestamp) {
        if (!timestamp) return '';

        const date = new Date(timestamp);
        const now = new Date();
        const diff = now - date;

        // Less than 1 minute
        if (diff < 60000) {
            return 'Just now';
        }

        // Less than 1 hour
        if (diff < 3600000) {
            const minutes = Math.floor(diff / 60000);
            return `${minutes}m ago`;
        }

        // Less than 24 hours
        if (diff < 86400000) {
            const hours = Math.floor(diff / 3600000);
            return `${hours}h ago`;
        }

        // More than 24 hours
        const days = Math.floor(diff / 86400000);
        if (days === 1) return 'Yesterday';
        if (days < 7) return `${days}d ago`;

        // Format as date
        return date.toLocaleDateString();
    }
}

// Initialize when DOM is ready
$(document).ready(() => {
    window.frostNotifications = new FrostAdminLTENotifications();
});
