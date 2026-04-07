<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ListingSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Listings & Vehicles';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 5;
    protected static ?string $title = 'Listing & Vehicle Settings';
    protected static string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::getGroup('listings');
        $this->form->fill($settings);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Listing Defaults')
                    ->description('Default values for vehicle listings')
                    ->icon('heroicon-o-truck')
                    ->schema([
                        Forms\Components\TextInput::make('per_page')
                            ->label('Listings Per Page')
                            ->numeric()
                            ->minValue(6)
                            ->maxValue(100)
                            ->helperText('Number of vehicles shown per search results page'),
                        Forms\Components\TextInput::make('max_vehicle_images')
                            ->label('Max Images Per Vehicle')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(50)
                            ->helperText('Maximum number of images a seller can upload'),
                        Forms\Components\TextInput::make('max_image_size_mb')
                            ->label('Max Image Size')
                            ->numeric()
                            ->suffix('MB')
                            ->helperText('Maximum file size per image upload'),
                        Forms\Components\TextInput::make('listing_duration_days')
                            ->label('Default Listing Duration')
                            ->numeric()
                            ->suffix('days')
                            ->helperText('How long a listing stays active'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Listing Options')
                    ->description('Toggle listing features')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->schema([
                        Forms\Components\Toggle::make('auto_approve_listings')
                            ->label('Auto-Approve Listings')
                            ->helperText('Automatically approve new listings (skip manual review)'),
                        Forms\Components\Toggle::make('show_phone_numbers')
                            ->label('Show Phone Numbers')
                            ->helperText('Display seller phone numbers on listings'),
                        Forms\Components\Toggle::make('enable_video_uploads')
                            ->label('Enable Video Uploads')
                            ->helperText('Allow sellers to add video URLs to listings'),
                        Forms\Components\Toggle::make('enable_price_negotiation')
                            ->label('Price Negotiation')
                            ->helperText('Allow buyers to make offers on vehicles'),
                        Forms\Components\Toggle::make('enable_test_drives')
                            ->label('Test Drive Requests')
                            ->helperText('Allow buyers to request test drives'),
                        Forms\Components\Toggle::make('enable_price_alerts')
                            ->label('Price Alerts')
                            ->helperText('Allow users to set price drop alerts'),
                        Forms\Components\Toggle::make('enable_saved_searches')
                            ->label('Saved Searches')
                            ->helperText('Allow users to save search filters'),
                        Forms\Components\Toggle::make('enable_vehicle_comparison')
                            ->label('Vehicle Comparison')
                            ->helperText('Allow users to compare vehicles side by side'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Featured Listings')
                    ->description('Configure premium listing placement')
                    ->icon('heroicon-o-star')
                    ->schema([
                        Forms\Components\Toggle::make('enable_featured')
                            ->label('Enable Featured Listings')
                            ->helperText('Allow sellers to promote listings'),
                        Forms\Components\TextInput::make('featured_price')
                            ->label('Featured Listing Price')
                            ->numeric()
                            ->prefix('€')
                            ->helperText('Cost to feature a listing'),
                        Forms\Components\TextInput::make('featured_duration_days')
                            ->label('Featured Duration')
                            ->numeric()
                            ->suffix('days')
                            ->helperText('How long a featured listing stays promoted'),
                        Forms\Components\TextInput::make('max_featured_per_user')
                            ->label('Max Featured Per User')
                            ->numeric()
                            ->helperText('Maximum active featured listings per user'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Search & Filters')
                    ->description('Configure search behavior')
                    ->icon('heroicon-o-magnifying-glass')
                    ->schema([
                        Forms\Components\TextInput::make('search_radius_km')
                            ->label('Default Search Radius')
                            ->numeric()
                            ->suffix('km')
                            ->helperText('Default distance radius for location-based search'),
                        Forms\Components\TextInput::make('similar_vehicles_count')
                            ->label('Similar Vehicles Count')
                            ->numeric()
                            ->helperText('Number of similar vehicles shown on listing page'),
                        Forms\Components\Toggle::make('enable_location_search')
                            ->label('Location Search')
                            ->helperText('Enable search by geographic location'),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::setValue("listings.{$key}", $value);
        }

        Notification::make()
            ->title('Listing settings saved')
            ->success()
            ->send();
    }
}
