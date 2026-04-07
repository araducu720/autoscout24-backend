<?php

namespace App\Notifications;

use App\Models\SafetradeTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TransactionStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $deleteWhenMissingModels = true;

    public function __construct(
        protected SafetradeTransaction $transaction,
        protected string $status,
        protected ?string $reason = null,
        protected ?string $trackingNumber = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusLabels = [
            'confirmed' => 'Transaction Confirmed',
            'in_transit' => 'Vehicle In Transit',
            'delivered' => 'Vehicle Delivered',
            'completed' => 'Transaction Completed',
            'cancelled' => 'Transaction Cancelled',
            'tracking_added' => 'Tracking Number Added',
        ];

        $label = $statusLabels[$this->status] ?? 'Transaction Updated';

        return (new MailMessage)
            ->subject("{$label} - SafeTrade #{$this->transaction->reference}")
            ->view('emails.transaction-status', [
                'notifiable' => $notifiable,
                'transaction' => $this->transaction,
                'status' => $this->status,
                'statusLabel' => $label,
                'reason' => $this->reason,
                'trackingNumber' => $this->trackingNumber,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        $statusLabels = [
            'confirmed' => 'Transaction Confirmed',
            'in_transit' => 'Vehicle In Transit',
            'delivered' => 'Vehicle Delivered',
            'completed' => 'Transaction Completed',
            'cancelled' => 'Transaction Cancelled',
            'tracking_added' => 'Tracking Number Added',
        ];

        return [
            'type' => 'transaction',
            'title' => $statusLabels[$this->status] ?? 'Transaction Updated',
            'message' => "Transaction #{$this->transaction->reference} — {$this->transaction->vehicle_title}",
            'transaction_id' => $this->transaction->id,
            'reference' => $this->transaction->reference,
            'action_url' => '/dashboard/transactions/' . $this->transaction->id,
        ];
    }
}
