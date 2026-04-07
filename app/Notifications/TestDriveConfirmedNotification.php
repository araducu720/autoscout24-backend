<?php

namespace App\Notifications;

use App\Models\TestDriveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TestDriveConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $deleteWhenMissingModels = true;

    public function __construct(
        public TestDriveRequest $testDrive
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $vehicle = $this->testDrive->vehicle;

        return (new MailMessage)
            ->subject('Test Drive Confirmed - ' . $vehicle->title)
            ->view('emails.test-drive-confirmed', [
                'testDrive' => $this->testDrive,
                'vehicle' => $vehicle,
            ]);
    }
}
