<?php

namespace App\Notifications;

use App\Models\TestDriveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TestDriveRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $deleteWhenMissingModels = true;

    public function __construct(
        public TestDriveRequest $testDrive
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $vehicle = $this->testDrive->vehicle;

        return (new MailMessage)
            ->subject('New Test Drive Request for ' . $vehicle->title)
            ->view('emails.test-drive-request', [
                'seller' => $notifiable,
                'testDrive' => $this->testDrive,
                'vehicle' => $vehicle,
                'locale' => $notifiable->locale ?? 'en',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'test_drive_request',
            'test_drive_id' => $this->testDrive->id,
            'vehicle_id' => $this->testDrive->vehicle_id,
            'vehicle_title' => $this->testDrive->vehicle->title,
            'requester_name' => $this->testDrive->name,
            'preferred_date' => $this->testDrive->preferred_date->format('Y-m-d'),
            'preferred_time' => $this->testDrive->preferred_time->format('H:i'),
        ];
    }
}
