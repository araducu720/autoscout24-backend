<?php

namespace App\Notifications;

use App\Models\Review;
use App\Models\Vehicle;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReviewNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $deleteWhenMissingModels = true;

    public function __construct(
        protected Review $review,
        protected Vehicle $vehicle
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $vehicleTitle = $this->vehicle->title
            ?? ($this->vehicle->make?->name . ' ' . $this->vehicle->model?->name);

        return (new MailMessage)
            ->subject("New Review for {$vehicleTitle} - AutoScout24")
            ->view('emails.new-review', [
                'notifiable' => $notifiable,
                'review' => $this->review,
                'vehicleTitle' => $vehicleTitle,
                'vehicle' => $this->vehicle,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        $vehicleTitle = $this->vehicle->title ?? 'Your Vehicle';

        return [
            'type' => 'review',
            'title' => 'New Review Received',
            'message' => "New {$this->review->rating}-star review for {$vehicleTitle}",
            'vehicle_id' => $this->vehicle->id,
            'review_id' => $this->review->id,
            'action_url' => '/vehicles/' . $this->vehicle->id,
        ];
    }
}
