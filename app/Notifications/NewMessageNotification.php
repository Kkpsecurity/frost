<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Support\Str;
use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Thread;

class NewMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $message;
    protected $thread;

    /**
     * Create a new notification instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
        $this->thread = $message->thread;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        $channels = ['database'];

        // Add broadcast channel if real-time notifications are enabled
        if (config('messenger.realtime_notifications', false)) {
            $channels[] = 'broadcast';
        }

        return $channels;
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable): DatabaseMessage
    {
        return new DatabaseMessage([
            'type' => 'new_message',
            'message_id' => $this->message->id,
            'thread_id' => $this->thread->id,
            'thread_subject' => $this->thread->subject,
            'sender_name' => $this->message->user->name,
            'sender_id' => $this->message->user_id,
            'message_preview' => Str::limit($this->message->body, 100),
            'created_at' => $this->message->created_at,
        ]);
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'type' => 'new_message',
            'message_id' => $this->message->id,
            'thread_id' => $this->thread->id,
            'thread_subject' => $this->thread->subject,
            'sender_name' => $this->message->user->name,
            'sender_id' => $this->message->user_id,
            'message_preview' => Str::limit($this->message->body, 100),
            'created_at' => $this->message->created_at->format('c'),
        ]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => 'new_message',
            'message_id' => $this->message->id,
            'thread_id' => $this->thread->id,
            'thread_subject' => $this->thread->subject,
            'sender_name' => $this->message->user->name,
            'sender_id' => $this->message->user_id,
            'message_preview' => Str::limit($this->message->body, 100),
        ];
    }
}
