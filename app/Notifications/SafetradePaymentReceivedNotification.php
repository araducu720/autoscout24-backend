<?php

namespace App\Notifications;

use App\Models\SafetradeTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SafetradePaymentReceivedNotification extends Notification implements ShouldQueue
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
            ->subject('Payment Received - SafeTrade #' . $this->transaction->reference)
            ->view('emails.safetrade-payment-received', [
                'transaction' => $this->transaction,
                'notifiable' => $notifiable,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'transaction',
            'title' => 'Payment Received',
            'message' => "Payment of €" . number_format($this->transaction->amount, 2) . " received for {$this->transaction->vehicle_title}",
            'transaction_id' => $this->transaction->id,
            'reference' => $this->transaction->reference,
            'action_url' => '/dashboard/transactions/' . $this->transaction->id,
        ];
    }
}
