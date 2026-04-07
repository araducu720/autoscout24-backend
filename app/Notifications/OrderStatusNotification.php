<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $deleteWhenMissingModels = true;

    public function __construct(
        protected Order $order,
        protected string $action,
        protected ?string $reason = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subjects = [
            'new' => "New Order for Your Vehicle - AutoScout24",
            'accepted' => "Your Order Has Been Accepted - AutoScout24",
            'rejected' => "Your Order Has Been Declined - AutoScout24",
            'cancelled' => "Order Cancelled - AutoScout24",
        ];

        $vehicleTitle = $this->order->vehicle?->title
            ?? ($this->order->vehicle?->make?->name . ' ' . $this->order->vehicle?->model?->name)
            ?? 'Vehicle';

        return (new MailMessage)
            ->subject($subjects[$this->action] ?? "Order Update - AutoScout24")
            ->view('emails.order-status', [
                'notifiable' => $notifiable,
                'order' => $this->order,
                'action' => $this->action,
                'reason' => $this->reason,
                'vehicleTitle' => $vehicleTitle,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        $titles = [
            'new' => 'New Order Received',
            'accepted' => 'Order Accepted',
            'rejected' => 'Order Rejected',
            'cancelled' => 'Order Cancelled',
        ];

        $vehicleTitle = $this->order->vehicle?->title ?? 'Vehicle';

        return [
            'type' => 'order',
            'title' => $titles[$this->action] ?? 'Order Update',
            'message' => match($this->action) {
                'new' => "New order for {$vehicleTitle} - €" . number_format($this->order->total_price, 2),
                'accepted' => "Your order for {$vehicleTitle} has been accepted",
                'rejected' => "Your order for {$vehicleTitle} was declined" . ($this->reason ? ": {$this->reason}" : ''),
                'cancelled' => "Order for {$vehicleTitle} has been cancelled",
                default => "Order status updated for {$vehicleTitle}",
            },
            'order_id' => $this->order->id,
            'action_url' => '/dashboard/transactions',
        ];
    }
}
