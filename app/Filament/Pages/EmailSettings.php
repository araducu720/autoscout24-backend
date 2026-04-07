<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class EmailSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationLabel = 'Email & Notifications';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 4;
    protected static ?string $title = 'Email & Notification Settings';
    protected static string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::getGroup('email');
        $this->form->fill($settings);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Email Configuration')
                    ->description('SMTP and email sending configuration')
                    ->icon('heroicon-o-paper-airplane')
                    ->schema([
                        Forms\Components\TextInput::make('from_name')
                            ->label('Sender Name')
                            ->helperText('Name displayed in the "From" field of emails'),
                        Forms\Components\TextInput::make('from_email')
                            ->label('Sender Email')
                            ->email()
                            ->helperText('Email address used as the "From" address'),
                        Forms\Components\TextInput::make('reply_to')
                            ->label('Reply-To Email')
                            ->email()
                            ->helperText('Email address users reply to'),
                        Forms\Components\Select::make('mail_driver')
                            ->label('Mail Driver')
                            ->options([
                                'smtp' => 'SMTP',
                                'mailgun' => 'Mailgun',
                                'ses' => 'Amazon SES',
                                'postmark' => 'Postmark',
                                'sendgrid' => 'SendGrid',
                                'log' => 'Log (Testing)',
                            ]),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Email Templates')
                    ->description('Customize email content and branding')
                    ->icon('heroicon-o-paint-brush')
                    ->schema([
                        Forms\Components\TextInput::make('logo_url')
                            ->label('Email Logo URL')
                            ->url()
                            ->helperText('Logo displayed in email headers'),
                        Forms\Components\ColorPicker::make('primary_color')
                            ->label('Primary Color')
                            ->helperText('Main color used in email templates'),
                        Forms\Components\Textarea::make('email_footer')
                            ->label('Email Footer Text')
                            ->rows(3)
                            ->helperText('Text displayed at the bottom of all emails'),
                        Forms\Components\Textarea::make('email_signature')
                            ->label('Email Signature')
                            ->rows(3)
                            ->helperText('Signature displayed before footer'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Notification Preferences')
                    ->description('Default notification delivery settings')
                    ->icon('heroicon-o-bell')
                    ->schema([
                        Forms\Components\Toggle::make('notify_new_registration')
                            ->label('New Registration Alert')
                            ->helperText('Email admin when a new user registers'),
                        Forms\Components\Toggle::make('notify_new_listing')
                            ->label('New Listing Alert')
                            ->helperText('Email admin when a new vehicle is listed'),
                        Forms\Components\Toggle::make('notify_new_transaction')
                            ->label('New Transaction Alert')
                            ->helperText('Email admin on new SafeTrade transactions'),
                        Forms\Components\Toggle::make('notify_new_dispute')
                            ->label('New Dispute Alert')
                            ->helperText('Email admin when a dispute is opened'),
                        Forms\Components\Toggle::make('notify_contact_messages')
                            ->label('Contact Message Alert')
                            ->helperText('Email admin on new contact form submissions'),
                        Forms\Components\TextInput::make('admin_notification_email')
                            ->label('Admin Notification Email')
                            ->email()
                            ->helperText('Where admin notifications are sent'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Email Rate Limiting')
                    ->description('Prevent email spam')
                    ->icon('heroicon-o-funnel')
                    ->schema([
                        Forms\Components\TextInput::make('max_emails_per_hour')
                            ->label('Max Emails/Hour')
                            ->numeric()
                            ->helperText('Maximum transactional emails per hour per user'),
                        Forms\Components\TextInput::make('verification_resend_cooldown')
                            ->label('Verification Resend Cooldown')
                            ->numeric()
                            ->suffix('minutes')
                            ->helperText('Minutes before user can resend verification email'),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::setValue("email.{$key}", $value);
        }

        Notification::make()
            ->title('Email settings saved')
            ->success()
            ->send();
    }
}
