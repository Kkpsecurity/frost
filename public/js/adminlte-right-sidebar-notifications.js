/**
 * Frost AdminLTE Right Sidebar Notifications & Messages Integration
 * Uses AdminLTE right sidebar for notifications and messaging
 */
class FrostAdminLTENotifications {
    constructor() {
        this.notifications = [];
        this.messages = [];
        this.refreshInterval = 30000; // 30 seconds
        this.currentView = 'notifications'; // 'notifications' or 'messages'
        this.init();
    }

    async init() {
        // Wait for jQuery and AdminLTE to load
        if (typeof $ === 'undefined') {
            setTimeout(() => this.init(), 100);
            return;
        }

        try {
            // Ensure notification icons exist in navbar
            this.ensureNotificationIcons();

            // Customize right sidebar content
            this.setupRightSidebar();

            this.bindEvents();
            await this.loadNotifications();
            await this.loadMessages();

            // Ensure updateBadges function exists before calling
            if (typeof this.updateBadges === "function") {
                this.updateBadges();
            } else {
                console.warn(
                    "updateBadges function not found, calling individual badge updates"
                );
                this.updateNotificationsBadge();
                this.updateMessagesBadge();
            }

            console.log(
                "✅ Frost AdminLTE Right Sidebar Notifications initialized"
            );

            // Auto-refresh
            setInterval(() => {
                this.loadNotifications();
                this.loadMessages();
            }, this.refreshInterval);
        } catch (error) {
            console.error(
                "❌ Error initializing AdminLTE notifications:",
                error
            );
            // Initialize with empty data to prevent crashes
            this.notifications = [];
            this.messages = [];
            this.updateNotificationsBadge();
            this.updateMessagesBadge();
        }
    }

    ensureNotificationIcons() {
        const navbar = $('.navbar-nav.ml-auto, .navbar-nav:last-child');

        // Check if notification icons already exist
        if ($('#notifications-toggle').length === 0) {
            console.log('🔔 Adding notification bell to navbar');
            // Add notification bell (triggers right sidebar)
            navbar.prepend(`
                <li class="nav-item">
                    <a class="nav-link" href="#" id="notifications-toggle" data-widget="control-sidebar" data-view="notifications">
                        <i class="far fa-bell"></i>
                        <span class="badge badge-warning navbar-badge" style="display: none;">0</span>
                    </a>
                </li>
            `);
        } else {
            console.log('🔔 Notification bell already exists');
        }

        if ($('#messages-toggle').length === 0) {
            console.log('📧 Adding message envelope to navbar');
            // Add message envelope (triggers right sidebar)
            navbar.prepend(`
                <li class="nav-item">
                    <a class="nav-link" href="#" id="messages-toggle" data-widget="control-sidebar" data-view="messages">
                        <i class="far fa-envelope"></i>
                        <span class="badge badge-danger navbar-badge" style="display: none;">0</span>
                    </a>
                </li>
            `);
        } else {
            console.log('📧 Message envelope already exists');
        }
    }

    setupRightSidebar() {
        // Check if control sidebar exists
        if ($('.control-sidebar').length === 0) {
            // Create the control sidebar if it doesn't exist
            $('body').append(`
                <aside class="control-sidebar control-sidebar-dark">
                    <div class="p-3">
                        <div id="frost-sidebar-content">
                            <div class="tab-content">
                                <div id="notifications-tab" class="tab-pane active">
                                    <h5><i class="far fa-bell mr-2"></i>Notifications</h5>
                                    <div id="notifications-content">Loading...</div>
                                </div>
                                <div id="messages-tab" class="tab-pane">
                                    <h5><i class="far fa-envelope mr-2"></i>Messages</h5>
                                    <div id="messages-content">Loading...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>
            `);
        } else {
            // Modify existing control sidebar
            $('.control-sidebar').html(`
                <div class="p-3">
                    <div id="frost-sidebar-content">
                        <div class="tab-content">
                            <div id="notifications-tab" class="tab-pane active">
                                <h5><i class="far fa-bell mr-2"></i>Notifications</h5>
                                <div id="notifications-content">Loading...</div>
                            </div>
                            <div id="messages-tab" class="tab-pane">
                                <h5><i class="far fa-envelope mr-2"></i>Messages</h5>
                                <div id="messages-content">Loading...</div>
                            </div>
                        </div>
                    </div>
                </div>
            `);
        }
    }

    bindEvents() {
        // Handle notification icon click
        $(document).on('click', '#notifications-toggle', (e) => {
            e.preventDefault();
            this.switchToView('notifications');
        });

        // Handle messages icon click
        $(document).on('click', '#messages-toggle', (e) => {
            e.preventDefault();
            this.switchToView('messages');
        });

        // Mark all notifications as read
        $(document).on('click', '#mark-all-read', async (e) => {
            e.preventDefault();
            await this.markAllNotificationsRead();
        });

        // Handle sidebar close
        $(document).on('click', '[data-widget="control-sidebar"]', (e) => {
            const view = $(e.currentTarget).data('view');
            if (view === 'notifications') {
                this.showNotifications();
            } else if (view === 'messages') {
                this.showMessages();
            }
        });
    }

    async switchToView(viewType) {
        const isCurrentlyOpen = $('body').hasClass('control-sidebar-slide-open');
        const isSwitchingViews = this.currentView !== viewType;

        if (isCurrentlyOpen && isSwitchingViews) {
            $('body').removeClass('control-sidebar-slide-open');
            await new Promise(resolve => setTimeout(resolve, 600));
        }

        // Update content and load data
        if (viewType === 'notifications') {
            this.showNotifications();
        } else if (viewType === 'messages') {
            this.showMessages();
        }

        // Always open the sidebar after content is updated
        await new Promise(resolve => setTimeout(resolve, 100));
        $('body').addClass('control-sidebar-slide-open');
    }




    showNotifications() {
        this.currentView = 'notifications';
        $('#notifications-tab').addClass('active').show();
        $('#messages-tab').removeClass('active').hide();

        // Show loading state immediately
        $('#notifications-content').html('<div class="text-center p-3"><i class="fas fa-spinner fa-spin"></i> Loading notifications...</div>');

        // Update sidebar structure and load data
        this.updateNotificationsSidebar();
        this.loadNotifications();
    }

    showMessages() {
        this.currentView = 'messages';
        $('#messages-tab').addClass('active').show();
        $('#notifications-tab').removeClass('active').hide();

        // Show loading state immediately
        $('#messages-content').html('<div class="text-center p-3"><i class="fas fa-spinner fa-spin"></i> Loading messages...</div>');

        // Update sidebar structure and load data
        this.updateMessagesSidebar();
        this.loadMessages();
    }

    async loadNotifications() {
        try {
            const response = await fetch("/admin/messaging/notifications", {
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    Accept: "application/json",
                    "X-CSRF-TOKEN":
                        document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content") || "",
                },
                credentials: "same-origin",
            });

            if (response.ok) {
                this.notifications = await response.json();
                this.updateNotificationsBadge();
                if (this.currentView === 'notifications') {
                    this.updateNotificationsSidebar();
                }
            } else if (response.status === 401) {
                console.warn('Authentication required for notifications');
                this.notifications = [];
                this.updateNotificationsBadge();
            } else {
                console.warn('Failed to load notifications:', response.status);
                this.notifications = [];
                this.updateNotificationsBadge();
            }
        } catch (error) {
            console.error('Failed to load notifications:', error);
            this.notifications = [];
            this.updateNotificationsBadge();
        }
    }

    async loadMessages() {
        try {
            const response = await fetch("/admin/messaging/threads", {
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    Accept: "application/json",
                    "X-CSRF-TOKEN":
                        document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content") || "",
                },
                credentials: "same-origin",
            });

            if (response.ok) {
                this.messages = await response.json();
                this.updateMessagesBadge();
                if (this.currentView === 'messages') {
                    this.updateMessagesSidebar();
                }
            } else if (response.status === 401) {
                console.warn('Authentication required for messages');
                this.messages = [];
                this.updateMessagesBadge();
            } else {
                console.warn('Failed to load messages:', response.status);
                this.messages = [];
                this.updateMessagesBadge();
            }
        } catch (error) {
            console.error('Failed to load messages:', error);
            this.messages = [];
            this.updateMessagesBadge();
        }
    }

    updateBadges() {
        this.updateNotificationsBadge();
        this.updateMessagesBadge();
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

    updateNotificationsSidebar() {
        const container = $('#notifications-content');
        const html = this.renderNotificationsList();
        container.html(html);
    }

    updateMessagesSidebar() {
        const container = $('#messages-content');
        const html = this.renderMessagesList();
        container.html(html);
    }

    renderNotificationsList() {
        if (this.notifications.length === 0) {
            return `
                <div class="text-center text-muted py-4">
                    <i class="far fa-bell fa-3x mb-3"></i>
                    <p>No new notifications</p>
                </div>
            `;
        }

        let html = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">${this.notifications.length} Notifications</h6>
                <button class="btn btn-sm btn-outline-light mark-all-read">Mark all read</button>
            </div>
            <div class="notifications-list">
        `;

        this.notifications.forEach(notification => {
            const isRead = notification.read_at;
            const bgClass = isRead ? 'bg-secondary' : 'bg-info';

            html += `
                <div class="card mb-2 notification-item ${bgClass}" data-notification-id="${notification.id}">
                    <div class="card-body p-2">
                        <h6 class="card-title mb-1">${notification.title || 'Notification'}</h6>
                        <p class="card-text small mb-1">${notification.message}</p>
                        <small class="text-muted">${this.formatTime(notification.created_at)}</small>
                    </div>
                </div>
            `;
        });

        html += `
            </div>
            <div class="text-center mt-3">
                <a href="/messaging/notifications" class="btn btn-sm btn-light">View All Notifications</a>
            </div>
        `;

        return html;
    }

    renderMessagesList() {
        if (this.messages.length === 0) {
            return `
                <div class="text-center text-muted py-4">
                    <i class="far fa-envelope fa-3x mb-3"></i>
                    <p>No new messages</p>
                </div>
            `;
        }

        let html = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">${this.messages.length} Messages</h6>
                <button class="btn btn-sm btn-outline-light" onclick="location.href='/messaging'">New Message</button>
            </div>
            <div class="messages-list">
        `;

        this.messages.forEach(thread => {
            const hasUnread = thread.unread_count > 0;
            const bgClass = hasUnread ? 'bg-warning' : 'bg-secondary';

            html += `
                <div class="card mb-2 message-item ${bgClass}" data-thread-id="${thread.id}">
                    <div class="card-body p-2">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 mr-2">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                    <i class="far fa-user text-white small"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="card-title mb-1 ${hasUnread ? 'font-weight-bold' : ''}">${thread.title}</h6>
                                <p class="card-text small mb-1">${thread.last_message || 'No messages yet'}</p>
                                <small class="text-muted">${this.formatTime(thread.updated_at)}</small>
                                ${hasUnread ? `<span class="badge badge-danger badge-sm ml-2">${thread.unread_count}</span>` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        html += `
            </div>
            <div class="text-center mt-3">
                <a href="/messaging" class="btn btn-sm btn-light">View All Messages</a>
            </div>
        `;

        return html;
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
