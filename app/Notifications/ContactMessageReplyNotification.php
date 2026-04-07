<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContactMessageReplyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $deleteWhenMissingModels = true;

    public function __construct(
        public ContactMessage $contactMessage,
        public string $replySubject,
        public string $replyBody,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->replySubject)
            ->view('emails.contact-reply', [
                'contactMessage' => $this->contactMessage,
                'replyBody' => $this->replyBody,
                'replySubject' => $this->replySubject,
                'vehicleTitle' => $this->contactMessage->vehicle?->title ?? 'N/A',
            ]);
    }
}
