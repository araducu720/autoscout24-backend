<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class AppearanceSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';
    protected static ?string $navigationLabel = 'Appearance & Branding';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 6;
    protected static ?string $title = 'Appearance & Branding Settings';
    protected static string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::getGroup('appearance');
        $this->form->fill($settings);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Branding')
                    ->description('Logo, colors and visual identity')
                    ->icon('heroicon-o-swatch')
                    ->schema([
                        Forms\Components\TextInput::make('logo_url')
                            ->label('Logo URL')
                            ->url()
                            ->helperText('Main logo displayed in header'),
                        Forms\Components\TextInput::make('logo_dark_url')
                            ->label('Dark Mode Logo URL')
                            ->url()
                            ->helperText('Logo for dark mode/backgrounds'),
                        Forms\Components\TextInput::make('favicon_url')
                            ->label('Favicon URL')
                            ->url()
                            ->helperText('Browser tab icon'),
                        Forms\Components\ColorPicker::make('primary_color')
                            ->label('Primary Color')
                            ->helperText('Main brand color (buttons, links, accents)'),
                        Forms\Components\ColorPicker::make('secondary_color')
                            ->label('Secondary Color')
                            ->helperText('Secondary brand color'),
                        Forms\Components\ColorPicker::make('accent_color')
                            ->label('Accent Color')
                            ->helperText('Used for highlights and CTAs'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Homepage')
                    ->description('Homepage banner and content settings')
                    ->icon('heroicon-o-home')
                    ->schema([
                        Forms\Components\TextInput::make('hero_title')
                            ->label('Hero Title')
                            ->helperText('Main headline on the homepage'),
                        Forms\Components\TextInput::make('hero_subtitle')
                            ->label('Hero Subtitle')
                            ->helperText('Subtitle beneath the hero title'),
                        Forms\Components\TextInput::make('hero_image_url')
                            ->label('Hero Background Image')
                            ->url()
                            ->helperText('Background image for homepage hero section'),
                        Forms\Components\TextInput::make('hero_cta_text')
                            ->label('CTA Button Text')
                            ->helperText('Text on the main call-to-action button'),
                        Forms\Components\TextInput::make('hero_cta_url')
                            ->label('CTA Button Link')
                            ->helperText('URL the CTA button links to'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Footer')
                    ->description('Footer content and links')
                    ->icon('heroicon-o-bars-3')
                    ->schema([
                        Forms\Components\Textarea::make('footer_text')
                            ->label('Footer Text')
                            ->rows(3)
                            ->helperText('Copyright and legal text in footer'),
                        Forms\Components\TextInput::make('footer_facebook')
                            ->label('Facebook URL')
                            ->url(),
                        Forms\Components\TextInput::make('footer_twitter')
                            ->label('Twitter/X URL')
                            ->url(),
                        Forms\Components\TextInput::make('footer_instagram')
                            ->label('Instagram URL')
                            ->url(),
                        Forms\Components\TextInput::make('footer_linkedin')
                            ->label('LinkedIn URL')
                            ->url(),
                        Forms\Components\TextInput::make('footer_youtube')
                            ->label('YouTube URL')
                            ->url(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Custom Code')
                    ->description('Inject custom CSS/JS into the frontend')
                    ->icon('heroicon-o-code-bracket')
                    ->schema([
                        Forms\Components\Textarea::make('custom_css')
                            ->label('Custom CSS')
                            ->rows(6)
                            ->helperText('Custom CSS injected into all pages'),
                        Forms\Components\Textarea::make('custom_head_scripts')
                            ->label('Head Scripts')
                            ->rows(6)
                            ->helperText('Custom scripts injected before </head>'),
                        Forms\Components\Textarea::make('custom_body_scripts')
                            ->label('Body Scripts')
                            ->rows(6)
                            ->helperText('Custom scripts injected before </body>'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::setValue("appearance.{$key}", $value);
        }

        Notification::make()
            ->title('Appearance settings saved')
            ->success()
            ->send();
    }
}
