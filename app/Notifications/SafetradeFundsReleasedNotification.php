<?php

namespace App\Notifications;

use App\Models\SafetradeTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SafetradeFundsReleasedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $deleteWhenMissingModels = true;

    public function __construct(
        protected SafetradeTransaction $transaction
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Funds Released - SafeTrade #' . $this->transaction->reference)
            ->view('emails.safetrade-funds-released', [
                'transaction' => $this->transaction,
                'notifiable' => $notifiable,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'transaction',
            'title' => 'Funds Released',
            'message' => "€" . number_format($this->transaction->vehicle_price, 2) . " released for {$this->transaction->vehicle_title}",
            'transaction_id' => $this->transaction->id,
            'reference' => $this->transaction->reference,
            'action_url' => '/dashboard/transactions/' . $this->transaction->id,
        ];
    }
}
