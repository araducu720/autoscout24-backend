<?php

namespace App\Notifications;

use App\Models\SafetradeTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SafetradeDeliveryConfirmedNotification extends Notification implements ShouldQueue
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
            ->subject('Delivery Confirmed - SafeTrade #' . $this->transaction->reference)
            ->view('emails.safetrade-delivery-confirmed', [
                'transaction' => $this->transaction,
                'notifiable' => $notifiable,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'transaction',
            'title' => 'Delivery Confirmed',
            'message' => "Delivery confirmed for {$this->transaction->vehicle_title}. Funds being released.",
            'transaction_id' => $this->transaction->id,
            'reference' => $this->transaction->reference,
            'action_url' => '/dashboard/transactions/' . $this->transaction->id,
        ];
    }
}
