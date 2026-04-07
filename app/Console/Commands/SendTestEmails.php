<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Mail;

class SendTestEmails extends Command
{
    protected $signature = 'test:send-all-emails {email}';
    protected $description = 'Send test versions of all 13 email templates to a given address';

    public function handle(): int
    {
        $email = $this->argument('email');
        $this->info("Sending all email templates to: {$email}");

        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("User with email {$email} not found.");
            return 1;
        }

        $frontendUrl = config('app.frontend_url', 'https://www.autoscout24safetrade.com');
        $sent = 0;
        $failed = 0;

        // 1. Password Reset
        $this->sendTemplate($email, 'Reset Your Password - AutoScout24', 'emails.password-reset', [
            'user' => $user,
            'resetUrl' => $frontendUrl . '/reset-password?token=test-token-123&email=' . urlencode($email),
        ], $sent, $failed);

        // 2. Email Verification
        $this->sendTemplate($email, 'Verify Your Email Address - AutoScout24', 'emails.email-verification', [
            'user' => $user,
            'verificationUrl' => $frontendUrl . '/verify-email?id=' . $user->id . '&hash=test-hash-123',
        ], $sent, $failed);

        // 3. Contact Message Received
        $contactMessage = $this->makeFakeContactMessage($user);
        $this->sendTemplate($email, 'We received your message - AutoScout24', 'emails.contact-received', [
            'contactMessage' => $contactMessage,
            'vehicleTitle' => 'BMW 320d xDrive M Sport 2023',
        ], $sent, $failed);

        // 4. Contact Reply
        $this->sendTemplate($email, 'Reply to your message - AutoScout24', 'emails.contact-reply', [
            'contactMessage' => $contactMessage,
            'vehicleTitle' => 'BMW 320d xDrive M Sport 2023',
            'replyBody' => "Thank you for your interest in this BMW 320d xDrive.\n\nThe vehicle is still available and in excellent condition. I'd be happy to arrange a viewing at your convenience.\n\nPlease let me know when works best for you.",
        ], $sent, $failed);

        // 5. Test Drive Request (to seller)
        $testDrive = $this->makeFakeTestDrive($user);
        $vehicle = $this->makeFakeVehicle();
        $this->sendTemplate($email, 'New Test Drive Request - AutoScout24', 'emails.test-drive-request', [
            'seller' => $user,
            'testDrive' => $testDrive,
            'vehicle' => $vehicle,
        ], $sent, $failed);

        // 6. Test Drive Confirmed
        $this->sendTemplate($email, 'Test Drive Confirmed - AutoScout24', 'emails.test-drive-confirmed', [
            'testDrive' => $testDrive,
            'vehicle' => $vehicle,
        ], $sent, $failed);

        // 7. SafeTrade New Order
        $transaction = $this->makeFakeTransaction();
        $this->sendTemplate($email, 'New Vehicle Order - AutoScout24 SafeTrade', 'emails.safetrade-new-order', [
            'notifiable' => $user,
            'transaction' => $transaction,
        ], $sent, $failed);

        // 8. SafeTrade Payment Received
        $this->sendTemplate($email, 'Payment Received - AutoScout24 SafeTrade', 'emails.safetrade-payment-received', [
            'notifiable' => $user,
            'transaction' => $transaction,
        ], $sent, $failed);

        // 9. SafeTrade Delivery Confirmed
        $this->sendTemplate($email, 'Delivery Confirmed - AutoScout24 SafeTrade', 'emails.safetrade-delivery-confirmed', [
            'notifiable' => $user,
            'transaction' => $transaction,
        ], $sent, $failed);

        // 10. SafeTrade Funds Released
        $this->sendTemplate($email, 'Funds Released - AutoScout24 SafeTrade', 'emails.safetrade-funds-released', [
            'notifiable' => $user,
            'transaction' => $transaction,
        ], $sent, $failed);

        // 11. SafeTrade Dispute
        $this->sendTemplate($email, 'Dispute Opened - AutoScout24 SafeTrade', 'emails.safetrade-dispute', [
            'notifiable' => $user,
            'transaction' => $transaction,
            'reason' => 'Vehicle does not match the description provided in the listing. The mileage appears to be significantly higher than advertised.',
        ], $sent, $failed);

        // 12. Price Alert
        $this->sendTemplate($email, 'Price Alert - AutoScout24', 'emails.price-alert', [
            'notifiable' => $user,
            'vehicle' => $vehicle,
            'oldPrice' => 45990,
            'newPrice' => 39990,
            'diff' => 6000,
            'percentChange' => 13,
        ], $sent, $failed);

        // 13. Weekly Digest
        $this->sendTemplate($email, 'Your Weekly Digest - AutoScout24', 'emails.weekly-digest', [
            'notifiable' => $user,
            'stats' => [
                'new_messages' => 3,
                'price_drops' => 2,
                'new_listings' => 5,
                'pending_transactions' => 1,
            ],
        ], $sent, $failed);

        $this->newLine();
        $this->info("=== COMPLETE: {$sent} sent, {$failed} failed ===");

        return $failed > 0 ? 1 : 0;
    }

    private function sendTemplate(string $email, string $subject, string $view, array $data, int &$sent, int &$failed): void
    {
        try {
            Mail::send($view, $data, function ($message) use ($email, $subject) {
                $message->to($email)
                        ->subject($subject);
            });
            $sent++;
            $this->info("  ✓ {$subject}");
        } catch (\Throwable $e) {
            $failed++;
            $this->error("  ✗ {$subject}: " . $e->getMessage());
        }
    }

    private function makeFakeContactMessage(User $user): object
    {
        return (object) [
            'name' => $user->name,
            'email' => $user->email,
            'message' => 'Hi, I am very interested in this vehicle. Is it still available? Could you tell me more about the service history and if there are any known issues? Thank you!',
            'vehicle_id' => 1,
            'vehicle' => (object) ['id' => 1, 'title' => 'BMW 320d xDrive M Sport 2023'],
            'created_at' => now(),
        ];
    }

    private function makeFakeTestDrive(User $user): object
    {
        return (object) [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => '+49 170 1234567',
            'preferred_date' => now()->addDays(3),
            'preferred_time' => now()->setTime(14, 0),
            'message' => 'I would like to test drive this vehicle. I am available in the afternoon.',
        ];
    }

    private function makeFakeVehicle(): object
    {
        return (object) [
            'id' => 1,
            'title' => 'BMW 320d xDrive M Sport 2023',
            'price' => 45990,
            'year' => 2023,
            'make' => (object) ['name' => 'BMW'],
            'model' => (object) ['name' => '320d xDrive'],
        ];
    }

    private function makeFakeTransaction(): object
    {
        return (object) [
            'id' => 1,
            'reference' => 'ST-2026-00042',
            'vehicle_title' => 'BMW 320d xDrive M Sport 2023',
            'vehicle_price' => 45990,
            'amount' => 45990,
            'payment_method' => 'bank_transfer',
            'status' => 'completed',
        ];
    }
}
