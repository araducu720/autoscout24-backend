<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class GeneralSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'General Settings';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 1;
    protected static ?string $title = 'General Settings';
    protected static string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::getGroup('general');
        $this->form->fill($settings);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Site Information')
                    ->description('Basic information about your application')
                    ->icon('heroicon-o-globe-alt')
                    ->schema([
                        Forms\Components\TextInput::make('site_name')
                            ->label('Site Name')
                            ->required()
                            ->maxLength(100)
                            ->helperText('The name of your website displayed in the header and title'),
                        Forms\Components\Textarea::make('site_description')
                            ->label('Site Description')
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('Short description used in meta tags and about sections'),
                        Forms\Components\TextInput::make('site_tagline')
                            ->label('Tagline')
                            ->maxLength(200)
                            ->helperText('A catchy slogan displayed on the homepage'),
                        Forms\Components\TextInput::make('site_url')
                            ->label('Site URL')
                            ->url()
                            ->helperText('The main URL of your frontend application'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Contact Information')
                    ->description('How users can reach you')
                    ->icon('heroicon-o-envelope')
                    ->schema([
                        Forms\Components\TextInput::make('contact_email')
                            ->label('Contact Email')
                            ->email()
                            ->required(),
                        Forms\Components\TextInput::make('support_email')
                            ->label('Support Email')
                            ->email(),
                        Forms\Components\TextInput::make('contact_phone')
                            ->label('Phone Number')
                            ->tel(),
                        Forms\Components\Textarea::make('contact_address')
                            ->label('Address')
                            ->rows(2),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Regional Settings')
                    ->description('Default locale, currency and country settings')
                    ->icon('heroicon-o-language')
                    ->schema([
                        Forms\Components\Select::make('default_locale')
                            ->label('Default Language')
                            ->options([
                                'en' => 'English',
                                'de' => 'Deutsch',
                                'fr' => 'Français',
                                'it' => 'Italiano',
                                'nl' => 'Nederlands',
                                'es' => 'Español',
                                'ro' => 'Română',
                            ])
                            ->required(),
                        Forms\Components\Select::make('default_currency')
                            ->label('Default Currency')
                            ->options([
                                'EUR' => 'Euro (€)',
                                'CHF' => 'Swiss Franc (CHF)',
                                'GBP' => 'British Pound (£)',
                                'USD' => 'US Dollar ($)',
                                'RON' => 'Romanian Leu (RON)',
                                'PLN' => 'Polish Zloty (PLN)',
                                'SEK' => 'Swedish Krona (SEK)',
                            ])
                            ->required(),
                        Forms\Components\Select::make('default_country')
                            ->label('Default Country')
                            ->options([
                                'DE' => 'Germany',
                                'AT' => 'Austria',
                                'CH' => 'Switzerland',
                                'FR' => 'France',
                                'IT' => 'Italy',
                                'NL' => 'Netherlands',
                                'BE' => 'Belgium',
                                'ES' => 'Spain',
                                'RO' => 'Romania',
                                'PL' => 'Poland',
                                'SE' => 'Sweden',
                                'UK' => 'United Kingdom',
                            ])
                            ->required(),
                        Forms\Components\Select::make('timezone')
                            ->label('Timezone')
                            ->options(collect(timezone_identifiers_list())
                                ->filter(fn ($tz) => str_starts_with($tz, 'Europe/'))
                                ->mapWithKeys(fn ($tz) => [$tz => $tz])
                                ->toArray())
                            ->searchable()
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Feature Toggles')
                    ->description('Enable or disable major features')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->schema([
                        Forms\Components\Toggle::make('registration_enabled')
                            ->label('User Registration')
                            ->helperText('Allow new users to register'),
                        Forms\Components\Toggle::make('dealer_registration_enabled')
                            ->label('Dealer Registration')
                            ->helperText('Allow dealers to sign up'),
                        Forms\Components\Toggle::make('maintenance_mode')
                            ->label('Maintenance Mode')
                            ->helperText('Show maintenance page to visitors'),
                        Forms\Components\Toggle::make('dark_mode_enabled')
                            ->label('Dark Mode')
                            ->helperText('Enable dark mode option for users'),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::setValue("general.{$key}", $value);
        }

        Notification::make()
            ->title('General settings saved')
            ->success()
            ->send();
    }
}
