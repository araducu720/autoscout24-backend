<?php

namespace Tests\Feature;

use App\Events\SafetradeDeliveryConfirmed;
use App\Events\SafetradeDisputeOpened;
use App\Events\SafetradeFundsReleased;
use App\Events\SafetradePaymentFunded;
use App\Events\SafetradeTransactionCreated;
use App\Models\ContactMessage;
use App\Models\NotificationPreference;
use App\Models\PriceAlert;
use App\Models\SafetradeTransaction;
use App\Models\TestDriveRequest;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleMake;
use App\Models\VehicleModel;
use App\Notifications\ContactMessageReceivedNotification;
use App\Notifications\ContactMessageReplyNotification;
use App\Notifications\EmailVerificationNotification;
use App\Notifications\PasswordResetNotification;
use App\Notifications\PriceAlertTriggeredNotification;
use App\Notifications\SafetradeDeliveryConfirmedNotification;
use App\Notifications\SafetradeDisputeNotification;
use App\Notifications\SafetradeFundsReleasedNotification;
use App\Notifications\SafetradeNewOrderNotification;
use App\Notifications\SafetradePaymentReceivedNotification;
use App\Notifications\TestDriveConfirmedNotification;
use App\Notifications\TestDriveRequestNotification;
use App\Notifications\WeeklyDigestNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationSystemTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $seller;
    private Vehicle $vehicle;
    private SafetradeTransaction $transaction;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['name' => 'Test Buyer', 'email' => 'buyer@test.com']);
        $this->seller = User::factory()->create(['name' => 'Test Seller', 'email' => 'seller@test.com']);

        // Create vehicle make & model for the vehicle factory
        $make = VehicleMake::create(['name' => 'BMW', 'slug' => 'bmw', 'type' => 'car']);
        $model = VehicleModel::create(['make_id' => $make->id, 'name' => 'Series 3', 'slug' => 'series-3']);

        $this->vehicle = Vehicle::factory()->create([
            'user_id' => $this->seller->id,
            'make_id' => $make->id,
            'model_id' => $model->id,
            'title' => 'BMW Series 3 2023',
            'price' => 35000.00,
        ]);

        $this->transaction = SafetradeTransaction::create([
            'reference' => 'AS24-ST-TEST-0001',
            'buyer_id' => $this->user->id,
            'seller_id' => $this->seller->id,
            'vehicle_id' => $this->vehicle->id,
            'vehicle_title' => $this->vehicle->title,
            'vehicle_price' => $this->vehicle->price,
            'amount' => 35000.00,
            'escrow_fee' => 350.00,
            'status' => 'pending',
            'escrow_status' => 'pending',
            'payment_status' => 'pending',
        ]);
    }

    // ═══════════════════════════════════════════════════════
    //  EMAIL VERIFICATION NOTIFICATION
    // ═══════════════════════════════════════════════════════

    public function test_email_verification_uses_mail_channel(): void
    {
        $notification = new EmailVerificationNotification();
        $this->assertEquals(['mail'], $notification->via($this->user));
    }

    public function test_email_verification_returns_mail_message(): void
    {
        $notification = new EmailVerificationNotification();
        $mail = $notification->toMail($this->user);

        $this->assertInstanceOf(MailMessage::class, $mail);
        $this->assertEquals('Verify Your Email Address - AutoScout24', $mail->subject);
    }

    public function test_email_verification_generates_signed_url(): void
    {
        $notification = new EmailVerificationNotification();
        $mail = $notification->toMail($this->user);

        // The view data should contain the user and a verification URL
        $this->assertArrayHasKey('user', $mail->viewData);
        $this->assertArrayHasKey('verificationUrl', $mail->viewData);
        $this->assertStringContainsString('verification.verify', $mail->viewData['verificationUrl']);
    }

    public function test_user_sends_email_verification(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();
        $user->sendEmailVerificationNotification();

        Notification::assertSentTo($user, EmailVerificationNotification::class);
    }

    // ═══════════════════════════════════════════════════════
    //  PASSWORD RESET NOTIFICATION
    // ═══════════════════════════════════════════════════════

    public function test_password_reset_uses_mail_channel(): void
    {
        $notification = new PasswordResetNotification('test-token-123');
        $this->assertEquals(['mail'], $notification->via($this->user));
    }

    public function test_password_reset_returns_mail_message_with_reset_url(): void
    {
        $notification = new PasswordResetNotification('test-token-123');
        $mail = $notification->toMail($this->user);

        $this->assertInstanceOf(MailMessage::class, $mail);
        $this->assertEquals('Reset Your Password - AutoScout24', $mail->subject);
        $this->assertArrayHasKey('resetUrl', $mail->viewData);
        $this->assertStringContainsString('test-token-123', $mail->viewData['resetUrl']);
        $this->assertStringContainsString(urlencode($this->user->email), $mail->viewData['resetUrl']);
    }

    public function test_password_reset_uses_mailersend_mailer(): void
    {
        $notification = new PasswordResetNotification('token');
        $mail = $notification->toMail($this->user);

        // MailMessage stores mailer in the 'mailer' property
        $this->assertEquals('mailersend', $mail->mailer);
    }

    public function test_user_sends_password_reset_notification(): void
    {
        Notification::fake();

        $this->user->sendPasswordResetNotification('some-token');

        Notification::assertSentTo($this->user, PasswordResetNotification::class, function ($notification) {
            return $notification->token === 'some-token';
        });
    }

    // ═══════════════════════════════════════════════════════
    //  CONTACT MESSAGE RECEIVED NOTIFICATION
    // ═══════════════════════════════════════════════════════

    public function test_contact_received_uses_mail_channel(): void
    {
        $message = $this->createContactMessage();
        $notification = new ContactMessageReceivedNotification($message);
        $this->assertEquals(['mail'], $notification->via($this->user));
    }

    public function test_contact_received_returns_mail_with_correct_subject(): void
    {
        $message = $this->createContactMessage();
        $notification = new ContactMessageReceivedNotification($message);
        $mail = $notification->toMail($this->user);

        $this->assertInstanceOf(MailMessage::class, $mail);
        $this->assertEquals('We received your message - AutoScout24', $mail->subject);
        $this->assertEquals('mailersend', $mail->mailer);
    }

    public function test_contact_received_passes_vehicle_title(): void
    {
        $message = $this->createContactMessage();
        $notification = new ContactMessageReceivedNotification($message);
        $mail = $notification->toMail($this->user);

        $this->assertArrayHasKey('contactMessage', $mail->viewData);
        $this->assertArrayHasKey('vehicleTitle', $mail->viewData);
    }

    // ═══════════════════════════════════════════════════════
    //  CONTACT MESSAGE REPLY NOTIFICATION
    // ═══════════════════════════════════════════════════════

    public function test_contact_reply_uses_mail_channel(): void
    {
        $message = $this->createContactMessage();
        $notification = new ContactMessageReplyNotification($message, 'Re: Your inquiry', 'Thank you for reaching out.');
        $this->assertEquals(['mail'], $notification->via($this->user));
    }

    public function test_contact_reply_returns_mail_with_custom_subject(): void
    {
        $message = $this->createContactMessage();
        $notification = new ContactMessageReplyNotification($message, 'Re: Vehicle Inquiry', 'Here is your reply.');
        $mail = $notification->toMail($this->user);

        $this->assertInstanceOf(MailMessage::class, $mail);
        $this->assertEquals('Re: Vehicle Inquiry', $mail->subject);
        $this->assertEquals('mailersend', $mail->mailer);
        $this->assertEquals('Here is your reply.', $mail->viewData['replyBody']);
        $this->assertEquals('Re: Vehicle Inquiry', $mail->viewData['replySubject']);
    }

    // ═══════════════════════════════════════════════════════
    //  SAFETRADE NEW ORDER NOTIFICATION
    // ═══════════════════════════════════════════════════════

    public function test_safetrade_new_order_uses_mail_and_database_channels(): void
    {
        $notification = new SafetradeNewOrderNotification($this->transaction);
        $this->assertEquals(['mail', 'database'], $notification->via($this->seller));
    }

    public function test_safetrade_new_order_mail_contains_reference(): void
    {
        $notification = new SafetradeNewOrderNotification($this->transaction);
        $mail = $notification->toMail($this->seller);

        $this->assertInstanceOf(MailMessage::class, $mail);
        $this->assertStringContainsString($this->transaction->reference, $mail->subject);
        $this->assertEquals('mailersend', $mail->mailer);
    }

    public function test_safetrade_new_order_database_payload(): void
    {
        $notification = new SafetradeNewOrderNotification($this->transaction);
        $data = $notification->toArray($this->seller);

        $this->assertEquals('transaction', $data['type']);
        $this->assertEquals('New Vehicle Order', $data['title']);
        $this->assertEquals($this->transaction->id, $data['transaction_id']);
        $this->assertEquals($this->transaction->reference, $data['reference']);
        $this->assertStringContainsString($this->transaction->vehicle_title, $data['message']);
        $this->assertStringContainsString('/dashboard/transactions/', $data['action_url']);
    }

    // ═══════════════════════════════════════════════════════
    //  SAFETRADE PAYMENT RECEIVED NOTIFICATION
    // ═══════════════════════════════════════════════════════

    public function test_safetrade_payment_uses_mail_and_database(): void
    {
        $notification = new SafetradePaymentReceivedNotification($this->transaction);
        $this->assertEquals(['mail', 'database'], $notification->via($this->user));
    }

    public function test_safetrade_payment_mail_subject_contains_reference(): void
    {
        $notification = new SafetradePaymentReceivedNotification($this->transaction);
        $mail = $notification->toMail($this->user);

        $this->assertInstanceOf(MailMessage::class, $mail);
        $this->assertStringContainsString('Payment Received', $mail->subject);
        $this->assertStringContainsString($this->transaction->reference, $mail->subject);
        $this->assertEquals('mailersend', $mail->mailer);
    }

    public function test_safetrade_payment_database_payload(): void
    {
        $notification = new SafetradePaymentReceivedNotification($this->transaction);
        $data = $notification->toArray($this->user);

        $this->assertEquals('transaction', $data['type']);
        $this->assertEquals('Payment Received', $data['title']);
        $this->assertEquals($this->transaction->id, $data['transaction_id']);
        $this->assertStringContainsString($this->transaction->vehicle_title, $data['message']);
    }

    // ═══════════════════════════════════════════════════════
    //  SAFETRADE DELIVERY CONFIRMED NOTIFICATION
    // ═══════════════════════════════════════════════════════

    public function test_safetrade_delivery_uses_mail_and_database(): void
    {
        $notification = new SafetradeDeliveryConfirmedNotification($this->transaction);
        $this->assertEquals(['mail', 'database'], $notification->via($this->seller));
    }

    public function test_safetrade_delivery_mail_content(): void
    {
        $notification = new SafetradeDeliveryConfirmedNotification($this->transaction);
        $mail = $notification->toMail($this->seller);

        $this->assertInstanceOf(MailMessage::class, $mail);
        $this->assertStringContainsString('Delivery Confirmed', $mail->subject);
        $this->assertEquals('mailersend', $mail->mailer);
    }

    public function test_safetrade_delivery_database_payload(): void
    {
        $notification = new SafetradeDeliveryConfirmedNotification($this->transaction);
        $data = $notification->toArray($this->seller);

        $this->assertEquals('transaction', $data['type']);
        $this->assertEquals('Delivery Confirmed', $data['title']);
        $this->assertStringContainsString('Funds being released', $data['message']);
    }

    // ═══════════════════════════════════════════════════════
    //  SAFETRADE FUNDS RELEASED NOTIFICATION
    // ═══════════════════════════════════════════════════════

    public function test_safetrade_funds_released_uses_mail_and_database(): void
    {
        $notification = new SafetradeFundsReleasedNotification($this->transaction);
        $this->assertEquals(['mail', 'database'], $notification->via($this->seller));
    }

    public function test_safetrade_funds_released_mail_content(): void
    {
        $notification = new SafetradeFundsReleasedNotification($this->transaction);
        $mail = $notification->toMail($this->seller);

        $this->assertInstanceOf(MailMessage::class, $mail);
        $this->assertStringContainsString('Funds Released', $mail->subject);
        $this->assertStringContainsString($this->transaction->reference, $mail->subject);
        $this->assertEquals('mailersend', $mail->mailer);
    }

    public function test_safetrade_funds_released_database_payload(): void
    {
        $notification = new SafetradeFundsReleasedNotification($this->transaction);
        $data = $notification->toArray($this->seller);

        $this->assertEquals('transaction', $data['type']);
        $this->assertEquals('Funds Released', $data['title']);
        $this->assertStringContainsString($this->transaction->vehicle_title, $data['message']);
    }

    // ═══════════════════════════════════════════════════════
    //  SAFETRADE DISPUTE NOTIFICATION
    // ═══════════════════════════════════════════════════════

    public function test_safetrade_dispute_uses_mail_and_database(): void
    {
        $notification = new SafetradeDisputeNotification($this->transaction, 'Vehicle not as described');
        $this->assertEquals(['mail', 'database'], $notification->via($this->user));
    }

    public function test_safetrade_dispute_mail_content(): void
    {
        $notification = new SafetradeDisputeNotification($this->transaction, 'Vehicle not as described');
        $mail = $notification->toMail($this->user);

        $this->assertInstanceOf(MailMessage::class, $mail);
        $this->assertStringContainsString('Dispute Opened', $mail->subject);
        $this->assertEquals('mailersend', $mail->mailer);
        $this->assertEquals('Vehicle not as described', $mail->viewData['reason']);
    }

    public function test_safetrade_dispute_database_payload_contains_reason(): void
    {
        $reason = 'Vehicle has undisclosed damage';
        $notification = new SafetradeDisputeNotification($this->transaction, $reason);
        $data = $notification->toArray($this->user);

        $this->assertEquals('transaction', $data['type']);
        $this->assertEquals('Dispute Opened', $data['title']);
        $this->assertStringContainsString($reason, $data['message']);
        $this->assertStringContainsString($this->transaction->vehicle_title, $data['message']);
    }

    // ═══════════════════════════════════════════════════════
    //  PRICE ALERT TRIGGERED NOTIFICATION
    // ═══════════════════════════════════════════════════════

    public function test_price_alert_uses_database_channel_always(): void
    {
        $alert = PriceAlert::factory()->create([
            'user_id' => $this->user->id,
            'vehicle_id' => $this->vehicle->id,
            'notify_email' => false,
        ]);

        $notification = new PriceAlertTriggeredNotification($alert, 35000, 30000);
        $channels = $notification->via($this->user);

        $this->assertContains('database', $channels);
        $this->assertNotContains('mail', $channels);
    }

    public function test_price_alert_includes_mail_when_notify_email_true(): void
    {
        $alert = PriceAlert::factory()->create([
            'user_id' => $this->user->id,
            'vehicle_id' => $this->vehicle->id,
            'notify_email' => true,
        ]);

        $notification = new PriceAlertTriggeredNotification($alert, 35000, 30000);
        $channels = $notification->via($this->user);

        $this->assertContains('database', $channels);
        $this->assertContains('mail', $channels);
    }

    public function test_price_alert_mail_contains_price_info(): void
    {
        $alert = PriceAlert::factory()->create([
            'user_id' => $this->user->id,
            'vehicle_id' => $this->vehicle->id,
            'notify_email' => true,
        ]);

        $notification = new PriceAlertTriggeredNotification($alert, 35000, 30000);
        $mail = $notification->toMail($this->user);

        $this->assertInstanceOf(MailMessage::class, $mail);
        $this->assertEquals('mailersend', $mail->mailer);
        $this->assertStringContainsString('Price Alert', $mail->subject);
        $this->assertEquals(35000, $mail->viewData['oldPrice']);
        $this->assertEquals(30000, $mail->viewData['newPrice']);
        $this->assertEquals(5000, $mail->viewData['diff']);
    }

    public function test_price_alert_database_payload(): void
    {
        $alert = PriceAlert::factory()->create([
            'user_id' => $this->user->id,
            'vehicle_id' => $this->vehicle->id,
            'target_price' => 28000,
        ]);

        $notification = new PriceAlertTriggeredNotification($alert, 35000, 28000);
        $data = $notification->toArray($this->user);

        $this->assertEquals('price_alert', $data['type']);
        $this->assertEquals($alert->id, $data['alert_id']);
        $this->assertEquals($this->vehicle->id, $data['vehicle_id']);
        $this->assertEquals(35000, $data['old_price']);
        $this->assertEquals(28000, $data['new_price']);
        $this->assertEquals(28000, $data['target_price']);
    }

    public function test_price_alert_percent_change_calculation(): void
    {
        $alert = PriceAlert::factory()->create([
            'user_id' => $this->user->id,
            'vehicle_id' => $this->vehicle->id,
            'notify_email' => true,
        ]);

        $notification = new PriceAlertTriggeredNotification($alert, 40000, 30000);
        $mail = $notification->toMail($this->user);

        // diff = 10000, percentChange = (10000/40000)*100 = 25.0
        $this->assertEquals(25.0, $mail->viewData['percentChange']);
    }

    // ═══════════════════════════════════════════════════════
    //  TEST DRIVE REQUEST NOTIFICATION
    // ═══════════════════════════════════════════════════════

    public function test_test_drive_request_uses_mail_and_database(): void
    {
        $testDrive = $this->createTestDriveRequest();
        $notification = new TestDriveRequestNotification($testDrive);
        $this->assertEquals(['mail', 'database'], $notification->via($this->seller));
    }

    public function test_test_drive_request_mail_content(): void
    {
        $testDrive = $this->createTestDriveRequest();
        $notification = new TestDriveRequestNotification($testDrive);
        $mail = $notification->toMail($this->seller);

        $this->assertInstanceOf(MailMessage::class, $mail);
        $this->assertStringContainsString('Test Drive Request', $mail->subject);
        $this->assertStringContainsString($this->vehicle->title, $mail->subject);
        $this->assertEquals('mailersend', $mail->mailer);
        $this->assertArrayHasKey('seller', $mail->viewData);
        $this->assertArrayHasKey('testDrive', $mail->viewData);
        $this->assertArrayHasKey('vehicle', $mail->viewData);
    }

    public function test_test_drive_request_database_payload(): void
    {
        $testDrive = $this->createTestDriveRequest();
        $notification = new TestDriveRequestNotification($testDrive);
        $data = $notification->toArray($this->seller);

        $this->assertEquals('test_drive_request', $data['type']);
        $this->assertEquals($testDrive->id, $data['test_drive_id']);
        $this->assertEquals($this->vehicle->id, $data['vehicle_id']);
        $this->assertEquals($this->vehicle->title, $data['vehicle_title']);
        $this->assertEquals('John Doe', $data['requester_name']);
    }

    // ═══════════════════════════════════════════════════════
    //  TEST DRIVE CONFIRMED NOTIFICATION
    // ═══════════════════════════════════════════════════════

    public function test_test_drive_confirmed_uses_mail_only(): void
    {
        $testDrive = $this->createTestDriveRequest();
        $notification = new TestDriveConfirmedNotification($testDrive);
        $this->assertEquals(['mail'], $notification->via($this->user));
    }

    public function test_test_drive_confirmed_mail_content(): void
    {
        $testDrive = $this->createTestDriveRequest();
        $notification = new TestDriveConfirmedNotification($testDrive);
        $mail = $notification->toMail($this->user);

        $this->assertInstanceOf(MailMessage::class, $mail);
        $this->assertStringContainsString('Test Drive Confirmed', $mail->subject);
        $this->assertStringContainsString($this->vehicle->title, $mail->subject);
        $this->assertEquals('mailersend', $mail->mailer);
    }

    // ═══════════════════════════════════════════════════════
    //  WEEKLY DIGEST NOTIFICATION
    // ═══════════════════════════════════════════════════════

    public function test_weekly_digest_uses_mail_only(): void
    {
        $notification = new WeeklyDigestNotification(['total_sales' => 5, 'revenue' => 175000]);
        $this->assertEquals(['mail'], $notification->via($this->user));
    }

    public function test_weekly_digest_mail_content(): void
    {
        $stats = [
            'total_sales' => 5,
            'revenue' => 175000,
            'new_listings' => 12,
            'views' => 340,
        ];

        $notification = new WeeklyDigestNotification($stats);
        $mail = $notification->toMail($this->user);

        $this->assertInstanceOf(MailMessage::class, $mail);
        $this->assertEquals('Your Weekly AutoScout24 SafeTrade Digest', $mail->subject);
        $this->assertEquals('mailersend', $mail->mailer);
        $this->assertEquals($stats, $mail->viewData['stats']);
        $this->assertSame($this->user, $mail->viewData['notifiable']);
    }

    // ═══════════════════════════════════════════════════════
    //  EVENT → LISTENER → NOTIFICATION INTEGRATION TESTS
    // ═══════════════════════════════════════════════════════

    public function test_safetrade_transaction_created_event_sends_notification_to_seller(): void
    {
        Notification::fake();

        event(new SafetradeTransactionCreated($this->transaction));

        Notification::assertSentTo($this->seller, SafetradeNewOrderNotification::class);
        Notification::assertNotSentTo($this->user, SafetradeNewOrderNotification::class);
    }

    public function test_safetrade_payment_funded_event_sends_notification_to_both_parties(): void
    {
        Notification::fake();

        event(new SafetradePaymentFunded($this->transaction, 'bank_transfer'));

        Notification::assertSentTo($this->user, SafetradePaymentReceivedNotification::class);
        Notification::assertSentTo($this->seller, SafetradePaymentReceivedNotification::class);
    }

    public function test_safetrade_delivery_confirmed_event_sends_notification_to_seller(): void
    {
        Notification::fake();

        event(new SafetradeDeliveryConfirmed($this->transaction));

        Notification::assertSentTo($this->seller, SafetradeDeliveryConfirmedNotification::class);
    }

    public function test_safetrade_funds_released_event_sends_notification_to_both_parties(): void
    {
        Notification::fake();

        event(new SafetradeFundsReleased($this->transaction));

        Notification::assertSentTo($this->seller, SafetradeFundsReleasedNotification::class);
        Notification::assertSentTo($this->user, SafetradeFundsReleasedNotification::class);
    }

    public function test_safetrade_dispute_event_sends_notification_to_both_parties(): void
    {
        Notification::fake();

        event(new SafetradeDisputeOpened($this->transaction, 'Item defective'));

        Notification::assertSentTo($this->user, SafetradeDisputeNotification::class);
        Notification::assertSentTo($this->seller, SafetradeDisputeNotification::class);
    }

    public function test_event_listener_handles_missing_seller_gracefully(): void
    {
        Notification::fake();

        // Set seller_id to a non-existent user
        $this->transaction->update(['seller_id' => 99999]);

        // Should not throw — the listener has a null check
        event(new SafetradeTransactionCreated($this->transaction));

        Notification::assertNothingSent();
    }

    public function test_event_listener_handles_missing_buyer_gracefully(): void
    {
        Notification::fake();

        // Set buyer_id to a non-existent user
        $this->transaction->update(['buyer_id' => 99999]);

        // Payment listener notifies both buyer & seller — should handle missing buyer
        event(new SafetradePaymentFunded($this->transaction, 'card'));

        // Seller should still receive notification
        Notification::assertSentTo($this->seller, SafetradePaymentReceivedNotification::class);
    }

    // ═══════════════════════════════════════════════════════
    //  NOTIFICATION PREFERENCES
    // ═══════════════════════════════════════════════════════

    public function test_user_gets_default_notification_preferences(): void
    {
        $prefs = $this->user->getNotificationPreference('email');

        $this->assertInstanceOf(NotificationPreference::class, $prefs);
        $this->assertTrue($prefs->bid_received);
        $this->assertTrue($prefs->payment_received);
        $this->assertTrue($prefs->price_alert);
        $this->assertTrue($prefs->weekly_digest);
        $this->assertFalse($prefs->marketing);
    }

    public function test_should_notify_returns_true_for_enabled_types(): void
    {
        $this->user->getNotificationPreference('email'); // creates defaults

        $this->assertTrue($this->user->shouldNotify('bid_received'));
        $this->assertTrue($this->user->shouldNotify('transaction_update'));
        $this->assertTrue($this->user->shouldNotify('price_alert'));
    }

    public function test_should_notify_returns_false_for_marketing_by_default(): void
    {
        $this->user->getNotificationPreference('email');

        $this->assertFalse($this->user->shouldNotify('marketing'));
    }

    public function test_should_notify_updates_when_preference_changed(): void
    {
        $prefs = $this->user->getNotificationPreference('email');
        $prefs->update(['weekly_digest' => false]);

        // Refresh relation
        $this->user->unsetRelation('notificationPreferences');
        $this->assertFalse($this->user->shouldNotify('weekly_digest'));
    }

    public function test_separate_channels_have_independent_preferences(): void
    {
        $emailPrefs = $this->user->getNotificationPreference('email');
        $pushPrefs = $this->user->getNotificationPreference('push');

        $emailPrefs->update(['weekly_digest' => false]);

        $this->assertFalse($this->user->shouldNotify('weekly_digest', 'email'));
        $this->assertTrue($this->user->shouldNotify('weekly_digest', 'push'));
    }

    public function test_notification_preferences_api_requires_auth(): void
    {
        $this->getJson('/api/v1/notifications/preferences')->assertStatus(401);
        $this->putJson('/api/v1/notifications/preferences', [])->assertStatus(401);
    }

    public function test_notification_preferences_api_returns_data(): void
    {
        $token = $this->user->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/v1/notifications/preferences');
        $response->assertStatus(200)->assertJsonStructure(['data']);
    }

    public function test_notification_preferences_api_updates_correctly(): void
    {
        $token = $this->user->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->putJson('/api/v1/notifications/preferences', [
            'channel' => 'email',
            'marketing' => true,
            'weekly_digest' => false,
            'price_alert' => false,
        ]);

        $response->assertStatus(200);

        $prefs = NotificationPreference::where('user_id', $this->user->id)
            ->where('channel', 'email')
            ->first();

        $this->assertTrue($prefs->marketing);
        $this->assertFalse($prefs->weekly_digest);
        $this->assertFalse($prefs->price_alert);
    }

    // ═══════════════════════════════════════════════════════
    //  NOTIFICATION API ENDPOINTS
    // ═══════════════════════════════════════════════════════

    public function test_notifications_endpoint_returns_paginated_list(): void
    {
        $token = $this->user->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/v1/notifications');
        $response->assertStatus(200);
    }

    public function test_unread_count_returns_zero_for_new_user(): void
    {
        $token = $this->user->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/v1/notifications/unread-count');
        $response->assertStatus(200)->assertJson(['count' => 0]);
    }

    public function test_mark_all_read_succeeds(): void
    {
        $token = $this->user->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->putJson('/api/v1/notifications/read-all');
        $response->assertStatus(200);
    }

    public function test_database_notification_is_persisted(): void
    {
        // Send a real notification (not faked) to test database persistence
        $this->seller->notify(new SafetradeNewOrderNotification($this->transaction));

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->seller->id,
            'notifiable_type' => User::class,
        ]);

        $notification = $this->seller->notifications()->first();
        $this->assertEquals('transaction', $notification->data['type']);
        $this->assertEquals('New Vehicle Order', $notification->data['title']);
    }

    public function test_unread_count_increments_after_notification(): void
    {
        $this->seller->notify(new SafetradeNewOrderNotification($this->transaction));

        $token = $this->seller->createToken('test')->plainTextToken;
        $response = $this->withToken($token)->getJson('/api/v1/notifications/unread-count');
        $response->assertStatus(200)->assertJson(['count' => 1]);
    }

    public function test_mark_all_read_clears_unread_count(): void
    {
        $this->seller->notify(new SafetradeNewOrderNotification($this->transaction));
        $this->seller->notify(new SafetradePaymentReceivedNotification($this->transaction));

        $token = $this->seller->createToken('test')->plainTextToken;

        // Mark all as read
        $this->withToken($token)->putJson('/api/v1/notifications/read-all');

        // Verify count is now 0
        $response = $this->withToken($token)->getJson('/api/v1/notifications/unread-count');
        $response->assertJson(['count' => 0]);
    }

    // ═══════════════════════════════════════════════════════
    //  NOTIFICATION PREFERENCE ENFORCEMENT IN LISTENERS
    // ═══════════════════════════════════════════════════════

    public function test_listener_respects_disabled_transaction_update_preference(): void
    {
        Notification::fake();

        // Disable transaction_update for seller
        NotificationPreference::create([
            'user_id' => $this->seller->id,
            'channel' => 'email',
            'transaction_update' => false,
            ...NotificationPreference::getDefaultPreferences(),
        ]);

        // Override the preference we just set via getDefaultPreferences
        NotificationPreference::where('user_id', $this->seller->id)
            ->where('channel', 'email')
            ->update(['transaction_update' => false]);

        event(new SafetradeTransactionCreated($this->transaction));

        Notification::assertNotSentTo($this->seller, SafetradeNewOrderNotification::class);
    }

    public function test_listener_respects_disabled_payment_received_preference(): void
    {
        Notification::fake();

        // Disable payment_received for buyer
        NotificationPreference::create([
            'user_id' => $this->user->id,
            'channel' => 'email',
            ...NotificationPreference::getDefaultPreferences(),
        ]);
        NotificationPreference::where('user_id', $this->user->id)
            ->where('channel', 'email')
            ->update(['payment_received' => false]);

        event(new SafetradePaymentFunded($this->transaction, 'bank_transfer'));

        // Buyer should NOT receive (opted out)
        Notification::assertNotSentTo($this->user, SafetradePaymentReceivedNotification::class);
        // Seller should still receive
        Notification::assertSentTo($this->seller, SafetradePaymentReceivedNotification::class);
    }

    public function test_listener_respects_disabled_dispute_update_preference(): void
    {
        Notification::fake();

        // Disable dispute_update for both parties
        foreach ([$this->user, $this->seller] as $u) {
            NotificationPreference::create([
                'user_id' => $u->id,
                'channel' => 'email',
                ...NotificationPreference::getDefaultPreferences(),
            ]);
            NotificationPreference::where('user_id', $u->id)
                ->where('channel', 'email')
                ->update(['dispute_update' => false]);
        }

        event(new SafetradeDisputeOpened($this->transaction, 'Damaged'));

        Notification::assertNotSentTo($this->user, SafetradeDisputeNotification::class);
        Notification::assertNotSentTo($this->seller, SafetradeDisputeNotification::class);
    }

    public function test_listener_sends_when_preferences_are_default(): void
    {
        Notification::fake();

        // No explicit preferences — defaults should allow sending
        event(new SafetradeTransactionCreated($this->transaction));

        Notification::assertSentTo($this->seller, SafetradeNewOrderNotification::class);
    }

    // ═══════════════════════════════════════════════════════
    //  EMAIL VERIFICATION MAILER
    // ═══════════════════════════════════════════════════════

    public function test_email_verification_uses_mailersend_mailer(): void
    {
        $notification = new EmailVerificationNotification();
        $mail = $notification->toMail($this->user);

        $this->assertEquals('mailersend', $mail->mailer);
    }

    // ═══════════════════════════════════════════════════════
    //  VEHICLE PRICE TRACKING OBSERVER
    // ═══════════════════════════════════════════════════════

    public function test_vehicle_price_change_tracks_history(): void
    {
        $originalPrice = (float) $this->vehicle->price;

        $this->vehicle->update(['price' => 30000.00]);
        $this->vehicle->refresh();

        $this->assertNotNull($this->vehicle->price_history);
        $this->assertIsArray($this->vehicle->price_history);
        $this->assertCount(1, $this->vehicle->price_history);
        $this->assertEquals($originalPrice, $this->vehicle->price_history[0]['price']);
    }

    public function test_vehicle_price_drop_increments_counter(): void
    {
        $this->vehicle->update(['price' => 30000.00]); // drop from 35000
        $this->vehicle->refresh();

        $this->assertEquals(1, $this->vehicle->price_drops_count);
    }

    public function test_vehicle_price_increase_does_not_increment_drops(): void
    {
        $this->vehicle->update(['price' => 40000.00]); // increase from 35000
        $this->vehicle->refresh();

        $this->assertEquals(0, $this->vehicle->price_drops_count ?? 0);
    }

    public function test_vehicle_original_price_set_on_first_change(): void
    {
        $originalPrice = (float) $this->vehicle->price;

        $this->vehicle->update(['price' => 30000.00]);
        $this->vehicle->refresh();

        $this->assertEquals($originalPrice, (float) $this->vehicle->original_price);
    }

    // ═══════════════════════════════════════════════════════
    //  SCHEDULED COMMANDS
    // ═══════════════════════════════════════════════════════

    public function test_check_price_alerts_command_triggers_on_price_change(): void
    {
        Notification::fake();

        $alert = PriceAlert::factory()->create([
            'user_id' => $this->user->id,
            'vehicle_id' => $this->vehicle->id,
            'target_price' => 30000,
            'current_price' => 35000,
            'alert_type' => 'below',
            'is_active' => true,
            'notify_email' => true,
        ]);

        // Drop vehicle price below target
        $this->vehicle->update(['price' => 28000.00]);

        $this->artisan('alerts:check-prices')
            ->assertExitCode(0);

        Notification::assertSentTo($this->user, PriceAlertTriggeredNotification::class);
    }

    public function test_check_price_alerts_command_skips_inactive_alerts(): void
    {
        Notification::fake();

        PriceAlert::factory()->create([
            'user_id' => $this->user->id,
            'vehicle_id' => $this->vehicle->id,
            'target_price' => 30000,
            'current_price' => 35000,
            'alert_type' => 'below',
            'is_active' => false,
            'notify_email' => true,
        ]);

        $this->vehicle->update(['price' => 28000.00]);

        $this->artisan('alerts:check-prices')
            ->assertExitCode(0);

        Notification::assertNothingSent();
    }

    public function test_check_price_alerts_command_skips_when_price_unchanged(): void
    {
        Notification::fake();

        PriceAlert::factory()->create([
            'user_id' => $this->user->id,
            'vehicle_id' => $this->vehicle->id,
            'target_price' => 30000,
            'current_price' => 35000.00,
            'alert_type' => 'below',
            'is_active' => true,
            'notify_email' => true,
        ]);

        // Don't change the vehicle price — alert should be skipped

        $this->artisan('alerts:check-prices')
            ->assertExitCode(0);

        Notification::assertNothingSent();
    }

    public function test_send_weekly_digest_command_runs_successfully(): void
    {
        $this->artisan('digest:send-weekly')
            ->assertExitCode(0);
    }

    // ═══════════════════════════════════════════════════════
    //  QUEUEABLE NOTIFICATIONS
    // ═══════════════════════════════════════════════════════

    public function test_price_alert_notification_is_queueable(): void
    {
        $alert = PriceAlert::factory()->create([
            'user_id' => $this->user->id,
            'vehicle_id' => $this->vehicle->id,
        ]);

        $notification = new PriceAlertTriggeredNotification($alert, 35000, 30000);

        $this->assertContains(
            \Illuminate\Bus\Queueable::class,
            array_keys(class_uses_recursive($notification))
        );
    }

    public function test_weekly_digest_notification_is_queueable(): void
    {
        $notification = new WeeklyDigestNotification(['sales' => 5]);

        $this->assertContains(
            \Illuminate\Bus\Queueable::class,
            array_keys(class_uses_recursive($notification))
        );
    }

    // ═══════════════════════════════════════════════════════
    //  EDGE CASES
    // ═══════════════════════════════════════════════════════

    public function test_contact_message_without_vehicle_shows_na(): void
    {
        $message = ContactMessage::create([
            'name' => 'Test User',
            'email' => 'test@test.com',
            'message' => 'General inquiry',
            'status' => 'new',
            // No vehicle_id
        ]);

        $notification = new ContactMessageReceivedNotification($message);
        $mail = $notification->toMail($this->user);

        $this->assertEquals('N/A', $mail->viewData['vehicleTitle']);
    }

    public function test_price_alert_with_zero_old_price_no_division_error(): void
    {
        $alert = PriceAlert::factory()->create([
            'user_id' => $this->user->id,
            'vehicle_id' => $this->vehicle->id,
            'notify_email' => true,
        ]);

        $notification = new PriceAlertTriggeredNotification($alert, 0, 30000);
        $mail = $notification->toMail($this->user);

        // Should not throw division by zero
        $this->assertEquals(0, $mail->viewData['percentChange']);
    }

    public function test_multiple_notifications_for_same_user(): void
    {
        $this->seller->notify(new SafetradeNewOrderNotification($this->transaction));
        $this->seller->notify(new SafetradePaymentReceivedNotification($this->transaction));
        $this->seller->notify(new SafetradeDeliveryConfirmedNotification($this->transaction));

        $this->assertEquals(3, $this->seller->unreadNotifications->count());
    }

    // ═══════════════════════════════════════════════════════
    //  HELPERS
    // ═══════════════════════════════════════════════════════

    private function createContactMessage(): ContactMessage
    {
        return ContactMessage::create([
            'vehicle_id' => $this->vehicle->id,
            'name' => 'John Doe',
            'email' => 'john@test.com',
            'message' => 'I am interested in this vehicle.',
            'status' => 'new',
        ]);
    }

    private function createTestDriveRequest(): TestDriveRequest
    {
        return TestDriveRequest::create([
            'vehicle_id' => $this->vehicle->id,
            'user_id' => $this->user->id,
            'name' => 'John Doe',
            'email' => 'john@test.com',
            'phone' => '+49123456789',
            'preferred_date' => now()->addDays(3)->toDateString(),
            'preferred_time' => '14:00:00',
            'message' => 'I would like a test drive.',
            'status' => 'pending',
        ]);
    }
}
