<?php

namespace App\Notifications;

use App\Models\PriceAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PriceAlertTriggeredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $deleteWhenMissingModels = true;

    public function __construct(
        private PriceAlert $alert,
        private float $oldPrice,
        private float $newPrice,
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['database'];
        if ($this->alert->notify_email) {
            $channels[] = 'mail';
        }
        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $vehicle = $this->alert->vehicle;
        $diff = $this->oldPrice - $this->newPrice;
        $percentChange = $this->oldPrice > 0 ? round(($diff / $this->oldPrice) * 100, 1) : 0;

        return (new MailMessage)
            ->subject("Price Alert: {$vehicle->title}")
            ->view('emails.price-alert', [
                'vehicle' => $vehicle,
                'oldPrice' => $this->oldPrice,
                'newPrice' => $this->newPrice,
                'diff' => $diff,
                'percentChange' => $percentChange,
                'notifiable' => $notifiable,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'price_alert',
            'alert_id' => $this->alert->id,
            'vehicle_id' => $this->alert->vehicle_id,
            'vehicle_title' => $this->alert->vehicle?->title,
            'old_price' => $this->oldPrice,
            'new_price' => $this->newPrice,
            'target_price' => $this->alert->target_price,
        ];
    }
}
