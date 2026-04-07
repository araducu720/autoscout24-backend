<?php

namespace App\Notifications;

use App\Models\Dispute;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DisputeUpdateNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $deleteWhenMissingModels = true;

    public function __construct(
        protected Dispute $dispute,
        protected string $action,
        protected ?string $details = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subjects = [
            'message' => 'New Dispute Message',
            'resolution_proposed' => 'Resolution Proposed',
            'resolution_accepted' => 'Resolution Accepted',
            'resolution_rejected' => 'Resolution Rejected',
            'resolved' => 'Dispute Resolved',
            'closed' => 'Dispute Closed',
            'admin_resolved' => 'Dispute Resolved by Admin',
        ];

        $reference = $this->dispute->transaction?->reference ?? 'N/A';
        $subject = ($subjects[$this->action] ?? 'Dispute Update') . " - SafeTrade #{$reference}";

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.dispute-update', [
                'notifiable' => $notifiable,
                'dispute' => $this->dispute,
                'action' => $this->action,
                'actionLabel' => $subjects[$this->action] ?? 'Dispute Update',
                'details' => $this->details,
                'reference' => $reference,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        $titles = [
            'message' => 'New Dispute Message',
            'resolution_proposed' => 'Resolution Proposed',
            'resolution_accepted' => 'Resolution Accepted',
            'resolution_rejected' => 'Resolution Rejected',
            'resolved' => 'Dispute Resolved',
            'closed' => 'Dispute Closed',
            'admin_resolved' => 'Dispute Resolved by Admin',
        ];

        return [
            'type' => 'dispute',
            'title' => $titles[$this->action] ?? 'Dispute Update',
            'message' => $this->details ?? "Dispute #{$this->dispute->id} has been updated",
            'dispute_id' => $this->dispute->id,
            'transaction_id' => $this->dispute->transaction_id,
            'action_url' => '/dashboard/transactions/' . $this->dispute->transaction_id,
        ];
    }
}
