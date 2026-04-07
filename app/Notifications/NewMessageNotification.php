<?php

namespace App\Notifications;

use App\Models\Conversation;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $deleteWhenMissingModels = true;

    public function __construct(
        protected Conversation $conversation,
        protected string $senderName,
        protected string $messagePreview
    ) {}

    public function via(object $notifiable): array
    {
        if ($notifiable->shouldNotify('new_message', 'email')) {
            return ['mail', 'database'];
        }
        return ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New Message from {$this->senderName} - AutoScout24")
            ->view('emails.new-message', [
                'notifiable' => $notifiable,
                'senderName' => $this->senderName,
                'messagePreview' => $this->messagePreview,
                'conversation' => $this->conversation,
                'vehicleTitle' => $this->conversation->vehicle?->title ?? null,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'message',
            'title' => 'New Message',
            'message' => "{$this->senderName}: {$this->messagePreview}",
            'conversation_id' => $this->conversation->id,
            'action_url' => '/messages',
        ];
    }
}
