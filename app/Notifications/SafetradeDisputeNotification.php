<?php

namespace App\Notifications;

use App\Models\SafetradeTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SafetradeDisputeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $deleteWhenMissingModels = true;

    public function __construct(
        protected SafetradeTransaction $transaction,
        protected string $reason
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Dispute Opened - SafeTrade #' . $this->transaction->reference)
            ->view('emails.safetrade-dispute', [
                'transaction' => $this->transaction,
                'reason' => $this->reason,
                'notifiable' => $notifiable,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'transaction',
            'title' => 'Dispute Opened',
            'message' => "Dispute opened for {$this->transaction->vehicle_title}: {$this->reason}",
            'transaction_id' => $this->transaction->id,
            'reference' => $this->transaction->reference,
            'action_url' => '/dashboard/transactions/' . $this->transaction->id,
        ];
    }
}
