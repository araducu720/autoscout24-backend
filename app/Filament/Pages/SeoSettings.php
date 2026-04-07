<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SeoSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';
    protected static ?string $navigationLabel = 'SEO & Analytics';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 3;
    protected static ?string $title = 'SEO & Analytics Settings';
    protected static string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::getGroup('seo');
        $this->form->fill($settings);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Meta Tags')
                    ->description('Default meta tags for search engines')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Forms\Components\TextInput::make('meta_title')
                            ->label('Default Meta Title')
                            ->maxLength(70)
                            ->helperText('Max 70 characters. Used when page has no specific title'),
                        Forms\Components\Textarea::make('meta_description')
                            ->label('Default Meta Description')
                            ->maxLength(160)
                            ->rows(3)
                            ->helperText('Max 160 characters. Used as default description in search results'),
                        Forms\Components\TagsInput::make('meta_keywords')
                            ->label('Meta Keywords')
                            ->helperText('Comma-separated keywords for the site'),
                        Forms\Components\TextInput::make('og_image')
                            ->label('Default OG Image URL')
                            ->url()
                            ->helperText('Default image for social media sharing'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Structured Data')
                    ->description('Schema.org structured data for rich snippets')
                    ->icon('heroicon-o-code-bracket')
                    ->schema([
                        Forms\Components\TextInput::make('organization_name')
                            ->label('Organization Name')
                            ->helperText('Used in Organization schema'),
                        Forms\Components\TextInput::make('organization_logo')
                            ->label('Organization Logo URL')
                            ->url()
                            ->helperText('Used in Organization schema logo'),
                        Forms\Components\Textarea::make('structured_data_extra')
                            ->label('Additional Structured Data (JSON-LD)')
                            ->rows(5)
                            ->helperText('Additional JSON-LD structured data to include'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Analytics & Tracking')
                    ->description('Third-party analytics and tracking codes')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        Forms\Components\TextInput::make('google_analytics_id')
                            ->label('Google Analytics ID')
                            ->placeholder('G-XXXXXXXXXX')
                            ->helperText('Google Analytics 4 measurement ID'),
                        Forms\Components\TextInput::make('google_tag_manager_id')
                            ->label('Google Tag Manager ID')
                            ->placeholder('GTM-XXXXXXX')
                            ->helperText('Google Tag Manager container ID'),
                        Forms\Components\TextInput::make('facebook_pixel_id')
                            ->label('Facebook Pixel ID')
                            ->helperText('Meta/Facebook Pixel tracking ID'),
                        Forms\Components\TextInput::make('hotjar_id')
                            ->label('Hotjar Site ID')
                            ->helperText('Hotjar heatmap and recording site ID'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Robots & Sitemap')
                    ->description('Search engine crawling configuration')
                    ->icon('heroicon-o-globe-alt')
                    ->schema([
                        Forms\Components\Toggle::make('robots_index')
                            ->label('Allow Search Indexing')
                            ->helperText('When off, tells search engines not to index the site'),
                        Forms\Components\Toggle::make('sitemap_enabled')
                            ->label('Generate Sitemap')
                            ->helperText('Automatically generate XML sitemap'),
                        Forms\Components\Textarea::make('robots_extra')
                            ->label('Extra Robots Rules')
                            ->rows(4)
                            ->helperText('Additional rules for robots.txt'),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Handle meta_keywords which is array from TagsInput
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            Setting::setValue("seo.{$key}", $value);
        }

        Notification::make()
            ->title('SEO settings saved')
            ->success()
            ->send();
    }
}
