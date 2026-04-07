<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WeeklyDigestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private array $stats,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Weekly AutoScout24 SafeTrade Digest')
            ->view('emails.weekly-digest', [
                'stats' => $this->stats,
                'notifiable' => $notifiable,
            ]);
    }
}
