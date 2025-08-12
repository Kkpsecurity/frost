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
        // Wait for AdminLTE to load
        if (typeof $ === 'undefined') {
            setTimeout(() => this.init(), 100);
            return;
        }

        this.bindEvents();
        await this.loadNotifications();
        await this.loadMessages();
        this.updateBadges();

        // Auto-refresh
        setInterval(() => {
            this.loadNotifications();
            this.loadMessages();
        }, this.refreshInterval);
    }

    bindEvents() {
        // Handle AdminLTE notification dropdown clicks
        $(document).on('click', '[id*="notifications-toggle"]', (e) => {
            this.onNotificationsDropdownOpen();
        });

        $(document).on('click', '[id*="messages-toggle"]', (e) => {
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
            }
        } catch (error) {
            console.error('Failed to load messages:', error);
        }
    }        // Close messaging panel
        $(document).on('click', '#close-messaging-panel', () => {
            this.closeMessagingPanel();
        });

        // Notification item click
        $(document).on('click', '.notification-item', (e) => {
            const notificationId = $(e.currentTarget).data('notification-id');
            this.handleNotificationClick(notificationId);
        });

        // Message item click
        $(document).on('click', '.message-item', (e) => {
            const threadId = $(e.currentTarget).data('thread-id');
            this.openThread(threadId);
        });

        // Thread item click in full panel
        $(document).on('click', '.thread-item', (e) => {
            const threadId = $(e.currentTarget).data('thread-id');
            this.openThread(threadId);
        });

        // Send message
        $(document).on('submit', '#message-form', (e) => {
            e.preventDefault();
            this.sendMessage();
        });

        // New message button
        $(document).on('click', '#new-message-btn', () => {
            this.showNewMessageModal();
        });

        // Close dropdowns when clicking outside
        $(document).on('click', (e) => {
            if (!$(e.target).closest('.dropdown').length) {
                this.closeAllDropdowns();
            }
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
                this.renderNotifications();
                this.updateNotificationsBadge();
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
                this.renderMessages();
                this.updateMessagesBadge();
                this.renderThreadsList();
            }
        } catch (error) {
            console.error('Failed to load messages:', error);
        }
    }

    renderNotifications() {
        const container = $('#notifications-list');
        const header = $('#notifications-header');

        const count = this.notifications.length;
        header.text(`${count} Notification${count !== 1 ? 's' : ''}`);

        if (count === 0) {
            container.html(`
                <div class="empty-state">
                    <i class="far fa-bell"></i>
                    <div>No new notifications</div>
                </div>
            `);
            return;
        }

        const notificationsHtml = this.notifications.map(notification => `
            <div class="notification-item unread" data-notification-id="${notification.id}">
                <div class="notification-content">
                    <div class="notification-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="notification-body">
                        <div class="notification-title">${notification.sender_name}</div>
                        <div class="notification-message">${this.escapeHtml(notification.message_preview)}</div>
                        <div class="notification-time">${this.formatTime(notification.created_at)}</div>
                    </div>
                </div>
            </div>
        `).join('');

        container.html(notificationsHtml);
    }

    renderMessages() {
        const container = $('#messages-list');
        const header = $('#messages-header');

        const unreadCount = this.messages.reduce((sum, msg) => sum + msg.unread, 0);
        header.text(`${unreadCount} Unread Message${unreadCount !== 1 ? 's' : ''}`);

        if (this.messages.length === 0) {
            container.html(`
                <div class="empty-state">
                    <i class="far fa-envelope"></i>
                    <div>No messages yet</div>
                </div>
            `);
            return;
        }

        // Show only messages with unread count > 0 in dropdown
        const unreadMessages = this.messages.filter(msg => msg.unread > 0).slice(0, 5);

        if (unreadMessages.length === 0) {
            container.html(`
                <div class="empty-state">
                    <i class="far fa-envelope"></i>
                    <div>No unread messages</div>
                </div>
            `);
            return;
        }

        const messagesHtml = unreadMessages.map(message => `
            <div class="message-item ${message.unread > 0 ? 'unread' : ''}" data-thread-id="${message.id}">
                <div class="message-content">
                    <div class="message-avatar">
                        ${this.getInitials(message.participants[0] || 'U')}
                    </div>
                    <div class="message-body">
                        <div class="message-sender">${message.subject || 'No Subject'}</div>
                        <div class="message-preview">${message.participants.join(', ')}</div>
                        <div class="message-time">${this.formatTime(message.last_message_at)}</div>
                    </div>
                    ${message.unread > 0 ? `<span class="badge badge-danger">${message.unread}</span>` : ''}
                </div>
            </div>
        `).join('');

        container.html(messagesHtml);
    }

    renderThreadsList() {
        const container = $('#threads-list');
        if (!container.length) return;

        if (this.messages.length === 0) {
            container.html('<div class="text-center p-3 text-muted">No messages yet</div>');
            return;
        }

        const threadsHtml = this.messages.map(thread => `
            <div class="thread-item ${thread.unread > 0 ? 'unread' : ''}" data-thread-id="${thread.id}">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="fw-bold">${thread.subject || 'No Subject'}</div>
                        <div class="small text-muted">${thread.participants.join(', ')}</div>
                        ${thread.last_message_at ? `<div class="small text-muted">${this.formatTime(thread.last_message_at)}</div>` : ''}
                    </div>
                    ${thread.unread > 0 ? `<span class="badge badge-primary">${thread.unread}</span>` : ''}
                </div>
            </div>
        `).join('');

        container.html(threadsHtml);
    }

    updateBadges() {
        this.updateNotificationsBadge();
        this.updateMessagesBadge();
    }

    updateNotificationsBadge() {
        const badge = $('#notifications-count');
        const count = this.notifications.length;

        if (count > 0) {
            badge.text(count > 99 ? '99+' : count).show();
            badge.addClass('new-notification');
            setTimeout(() => badge.removeClass('new-notification'), 3000);
        } else {
            badge.hide();
        }
    }

    updateMessagesBadge() {
        const badge = $('#messages-count');
        const count = this.messages.reduce((sum, msg) => sum + msg.unread, 0);

        if (count > 0) {
            badge.text(count > 99 ? '99+' : count).show();
            badge.addClass('new-notification');
            setTimeout(() => badge.removeClass('new-notification'), 3000);
        } else {
            badge.hide();
        }
    }

    toggleNotificationsDropdown() {
        const dropdown = $('#notifications-dropdown');
        this.closeAllDropdowns();
        dropdown.toggleClass('show');
        if (dropdown.hasClass('show')) {
            this.loadNotifications();
        }
    }

    toggleMessagesDropdown() {
        const dropdown = $('#messages-dropdown');
        this.closeAllDropdowns();
        dropdown.toggleClass('show');
        if (dropdown.hasClass('show')) {
            this.loadMessages();
        }
    }

    closeAllDropdowns() {
        $('.dropdown-menu').removeClass('show');
    }

    openMessagingPanel() {
        this.closeAllDropdowns();
        $('#messaging-panel').addClass('show');
        this.loadMessages();
    }

    closeMessagingPanel() {
        $('#messaging-panel').removeClass('show');
        $('#thread-view').hide();
        $('#threads-list-container').show();
    }

    async openThread(threadId) {
        try {
            this.closeAllDropdowns();
            this.openMessagingPanel();

            const response = await fetch(`/messaging/threads/${threadId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                this.currentThread = await response.json();
                this.renderThreadView();
                $('#threads-list-container').hide();
                $('#thread-view').show();
                await this.loadMessages(); // Refresh unread counts
            }
        } catch (error) {
            console.error('Failed to load thread:', error);
        }
    }

    renderThreadView() {
        const container = $('#thread-view');
        if (!this.currentThread) return;

        const messagesHtml = this.currentThread.messages.map(message => `
            <div class="message">
                <div class="d-flex justify-content-between">
                    <strong>${message.author}</strong>
                    <small class="text-muted">${this.formatTime(message.created_at)}</small>
                </div>
                <div class="mt-1">${this.escapeHtml(message.body)}</div>
            </div>
        `).join('');

        container.html(`
            <div class="d-flex align-items-center mb-3">
                <button type="button" class="btn btn-sm btn-outline-secondary me-2" onclick="window.frostTopbar.backToThreadsList()">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <div class="flex-grow-1">
                    <h6 class="mb-0">${this.currentThread.subject || 'No Subject'}</h6>
                    <div class="small text-muted">Participants: ${this.currentThread.participants.map(p => p.name).join(', ')}</div>
                </div>
            </div>
            <div class="messages-container">
                ${messagesHtml}
            </div>
            <form id="message-form" class="mt-3">
                <div class="input-group">
                    <textarea id="message-input" class="form-control" rows="2" placeholder="Type your message..." required></textarea>
                    <button type="submit" class="btn btn-primary">Send</button>
                </div>
            </form>
        `);
    }

    backToThreadsList() {
        $('#thread-view').hide();
        $('#threads-list-container').show();
        this.currentThread = null;
    }

    async sendMessage() {
        if (!this.currentThread) return;

        const input = $('#message-input');
        const message = input.val().trim();
        if (!message) return;

        try {
            const response = await fetch(`/messaging/threads/${this.currentThread.id}/message`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ body: message })
            });

            if (response.ok) {
                input.val('');
                await this.openThread(this.currentThread.id);
            } else {
                alert('Failed to send message');
            }
        } catch (error) {
            console.error('Failed to send message:', error);
            alert('Failed to send message');
        }
    }

    async handleNotificationClick(notificationId) {
        // Mark notification as read
        try {
            await fetch(`/messaging/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            await this.loadNotifications();
        } catch (error) {
            console.error('Failed to mark notification as read:', error);
        }
    }

    async markAllNotificationsRead() {
        try {
            // Mark all notifications as read
            const promises = this.notifications.map(notification =>
                fetch(`/messaging/notifications/${notification.id}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
            );

            await Promise.all(promises);
            await this.loadNotifications();
            this.closeAllDropdowns();
        } catch (error) {
            console.error('Failed to mark all notifications as read:', error);
        }
    }

    showNewMessageModal() {
        // This would show a modal for creating new messages
        // For now, just alert
        alert('New message functionality would be implemented with a modal here');
    }

    getInitials(name) {
        return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
    }

    formatTime(timestamp) {
        if (!timestamp) return '';
        const date = new Date(timestamp);
        const now = new Date();
        const diffHours = Math.abs(now - date) / 36e5;

        if (diffHours < 1) {
            return 'Just now';
        } else if (diffHours < 24) {
            return `${Math.floor(diffHours)}h ago`;
        } else {
            return date.toLocaleDateString();
        }
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize when DOM is ready
$(document).ready(() => {
    window.frostTopbar = new FrostTopbar();
});
