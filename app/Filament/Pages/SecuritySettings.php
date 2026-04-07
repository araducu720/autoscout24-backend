<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SecuritySettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';
    protected static ?string $navigationLabel = 'Security & Privacy';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 7;
    protected static ?string $title = 'Security & Privacy Settings';
    protected static string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::getGroup('security');
        $this->form->fill($settings);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Authentication')
                    ->description('Login and registration security')
                    ->icon('heroicon-o-finger-print')
                    ->schema([
                        Forms\Components\Toggle::make('require_email_verification')
                            ->label('Require Email Verification')
                            ->helperText('Users must verify email before accessing features'),
                        Forms\Components\Toggle::make('enable_two_factor')
                            ->label('Two-Factor Authentication')
                            ->helperText('Allow users to enable 2FA on their accounts'),
                        Forms\Components\TextInput::make('password_min_length')
                            ->label('Minimum Password Length')
                            ->numeric()
                            ->minValue(6)
                            ->maxValue(32),
                        Forms\Components\Toggle::make('password_require_uppercase')
                            ->label('Require Uppercase')
                            ->helperText('Password must contain uppercase letter'),
                        Forms\Components\Toggle::make('password_require_number')
                            ->label('Require Number')
                            ->helperText('Password must contain a number'),
                        Forms\Components\Toggle::make('password_require_special')
                            ->label('Require Special Character')
                            ->helperText('Password must contain a special character'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Rate Limiting')
                    ->description('Protect against abuse and brute force attacks')
                    ->icon('heroicon-o-shield-exclamation')
                    ->schema([
                        Forms\Components\TextInput::make('login_max_attempts')
                            ->label('Max Login Attempts')
                            ->numeric()
                            ->helperText('Failed login attempts before lockout'),
                        Forms\Components\TextInput::make('login_lockout_minutes')
                            ->label('Lockout Duration')
                            ->numeric()
                            ->suffix('minutes')
                            ->helperText('Minutes user is locked out after max attempts'),
                        Forms\Components\TextInput::make('api_rate_limit')
                            ->label('API Rate Limit')
                            ->numeric()
                            ->suffix('req/min')
                            ->helperText('Maximum API requests per minute per user'),
                        Forms\Components\TextInput::make('contact_form_rate')
                            ->label('Contact Form Rate')
                            ->numeric()
                            ->suffix('per hour')
                            ->helperText('Max contact form submissions per hour per IP'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Privacy & GDPR')
                    ->description('Data protection and privacy settings')
                    ->icon('heroicon-o-eye-slash')
                    ->schema([
                        Forms\Components\Toggle::make('cookie_consent_enabled')
                            ->label('Cookie Consent Banner')
                            ->helperText('Show GDPR cookie consent banner'),
                        Forms\Components\Toggle::make('data_export_enabled')
                            ->label('Data Export (GDPR)')
                            ->helperText('Allow users to export their personal data'),
                        Forms\Components\Toggle::make('account_deletion_enabled')
                            ->label('Account Deletion')
                            ->helperText('Allow users to delete their accounts'),
                        Forms\Components\TextInput::make('data_retention_days')
                            ->label('Data Retention Period')
                            ->numeric()
                            ->suffix('days')
                            ->helperText('Days to retain inactive user data (0 = forever)'),
                        Forms\Components\Textarea::make('privacy_policy_url')
                            ->label('Privacy Policy URL')
                            ->helperText('Link to your privacy policy'),
                        Forms\Components\Textarea::make('terms_of_service_url')
                            ->label('Terms of Service URL')
                            ->helperText('Link to your terms of service'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Session')
                    ->description('User session configuration')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Forms\Components\TextInput::make('session_lifetime_minutes')
                            ->label('Session Lifetime')
                            ->numeric()
                            ->suffix('minutes')
                            ->helperText('How long before a user session expires'),
                        Forms\Components\TextInput::make('token_expiry_days')
                            ->label('API Token Expiry')
                            ->numeric()
                            ->suffix('days')
                            ->helperText('Days before API tokens expire'),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::setValue("security.{$key}", $value);
        }

        Notification::make()
            ->title('Security settings saved')
            ->success()
            ->send();
    }
}
