/**
 * Frost Messaging System
 * Handles messaging functionality with AdminLTE integration
 */
class FrostMessaging {
    constructor() {
        this.threads = [];
        this.currentThread = null;
        this.unreadCount = 0;
        this.init();
    }

    async init() {
        this.bindEvents();
        await this.loadThreads();
        this.updateUnreadBadge();

        // Auto-refresh every 30 seconds
        setInterval(() => this.loadThreads(), 30000);
    }

    bindEvents() {
        // Toggle messaging panel
        $(document).on('click', '#messaging-toggle', () => {
            this.toggleMessagingPanel();
        });

        // Create new message
        $(document).on('click', '#new-message-btn', () => {
            this.showNewMessageModal();
        });

        // Thread click handler
        $(document).on('click', '.thread-item', (e) => {
            const threadId = $(e.currentTarget).data('thread-id');
            this.openThread(threadId);
        });

        // Send message
        $(document).on('submit', '#message-form', (e) => {
            e.preventDefault();
            this.sendMessage();
        });

        // Mark as read
        $(document).on('click', '.mark-read-btn', (e) => {
            const threadId = $(e.currentTarget).data('thread-id');
            this.markAsRead(threadId);
        });
    }

    async loadThreads() {
        try {
            const response = await fetch('/messaging/threads', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                this.threads = await response.json();
                this.renderThreadsList();
                this.updateUnreadCount();
            }
        } catch (error) {
            console.error('Failed to load threads:', error);
        }
    }

    renderThreadsList() {
        const container = $('#threads-list');
        if (!container.length) return;

        if (this.threads.length === 0) {
            container.html('<div class="text-muted text-center p-3">No messages yet</div>');
            return;
        }

        const threadsHtml = this.threads.map(thread => `
            <div class="thread-item border-bottom p-2 cursor-pointer ${thread.unread > 0 ? 'bg-light' : ''}"
                 data-thread-id="${thread.id}">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="fw-bold ${thread.unread > 0 ? 'text-primary' : ''}">${thread.subject || 'No Subject'}</div>
                        <div class="small text-muted">${thread.participants.join(', ')}</div>
                        ${thread.last_message_at ? `<div class="small text-muted">${new Date(thread.last_message_at).toLocaleDateString()}</div>` : ''}
                    </div>
                    ${thread.unread > 0 ? `<span class="badge bg-primary">${thread.unread}</span>` : ''}
                </div>
            </div>
        `).join('');

        container.html(threadsHtml);
    }

    updateUnreadCount() {
        this.unreadCount = this.threads.reduce((sum, thread) => sum + thread.unread, 0);
        this.updateUnreadBadge();
    }

    updateUnreadBadge() {
        const badge = $('#messaging-unread-badge');
        if (this.unreadCount > 0) {
            badge.text(this.unreadCount).removeClass('d-none');
        } else {
            badge.addClass('d-none');
        }
    }

    toggleMessagingPanel() {
        const panel = $('#messaging-panel');
        if (panel.hasClass('show')) {
            panel.removeClass('show');
        } else {
            panel.addClass('show');
            this.loadThreads();
        }
    }

    async openThread(threadId) {
        try {
            const response = await fetch(`/messaging/threads/${threadId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                this.currentThread = await response.json();
                this.renderThreadView();
                await this.loadThreads(); // Refresh to update unread counts
            }
        } catch (error) {
            console.error('Failed to load thread:', error);
        }
    }

    renderThreadView() {
        const container = $('#thread-view');
        if (!this.currentThread) return;

        const messagesHtml = this.currentThread.messages.map(message => `
            <div class="message mb-3">
                <div class="d-flex justify-content-between">
                    <strong>${message.author}</strong>
                    <small class="text-muted">${new Date(message.created_at).toLocaleString()}</small>
                </div>
                <div class="mt-1">${this.escapeHtml(message.body)}</div>
            </div>
        `).join('');

        container.html(`
            <div class="thread-header border-bottom pb-2 mb-3">
                <h6>${this.currentThread.subject || 'No Subject'}</h6>
                <div class="small text-muted">Participants: ${this.currentThread.participants.map(p => p.name).join(', ')}</div>
            </div>
            <div class="messages-container" style="max-height: 300px; overflow-y: auto;">
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

    async markAsRead(threadId) {
        try {
            await fetch(`/messaging/threads/${threadId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            await this.loadThreads();
        } catch (error) {
            console.error('Failed to mark as read:', error);
        }
    }

    showNewMessageModal() {
        // This would show a modal for creating new messages
        // For now, just alert
        alert('New message functionality would be implemented here');
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize messaging when DOM is ready
$(document).ready(() => {
    window.frostMessaging = new FrostMessaging();
});
