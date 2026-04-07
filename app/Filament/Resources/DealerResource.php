<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DealerResource\Pages;
use App\Models\Dealer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DealerResource extends Resource
{
    protected static ?string $model = Dealer::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    
    protected static ?string $navigationGroup = 'Safe Trade';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Company Information')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('company_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('type')
                            ->options([
                                'independent' => 'Independent',
                                'franchise' => 'Franchise',
                                'manufacturer' => 'Manufacturer',
                            ])
                            ->default('independent'),
                        Forms\Components\TextInput::make('registration_number')
                            ->label('Registration Number')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('tax_id')
                            ->label('Tax ID / VAT')
                            ->maxLength(50),
                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Contact Information')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->required()
                            ->maxLength(50),
                        Forms\Components\TextInput::make('website')
                            ->url()
                            ->maxLength(255),
                    ])->columns(3),
                    
                Forms\Components\Section::make('Address')
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->rows(2)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('city')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('postal_code')
                            ->maxLength(20),
                        Forms\Components\TextInput::make('country')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('latitude')
                            ->numeric()
                            ->step(0.000001),
                        Forms\Components\TextInput::make('longitude')
                            ->numeric()
                            ->step(0.000001),
                    ])->columns(3),
                    
                Forms\Components\Section::make('Services & Status')
                    ->schema([
                        Forms\Components\Toggle::make('offers_home_delivery')
                            ->label('Offers Home Delivery'),
                        Forms\Components\Toggle::make('offers_financing')
                            ->label('Offers Financing'),
                        Forms\Components\Toggle::make('offers_warranty')
                            ->label('Offers Warranty'),
                        Forms\Components\Toggle::make('is_verified')
                            ->label('Verified Dealer')
                            ->helperText('Verified dealers can access SafeTrade features'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                        Forms\Components\TextInput::make('rating')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(5)
                            ->step(0.1),
                        Forms\Components\TextInput::make('total_reviews')
                            ->numeric()
                            ->default(0)
                            ->disabled(),
                        Forms\Components\TextInput::make('total_purchases')
                            ->numeric()
                            ->default(0)
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('verified_at')
                            ->disabled(),
                    ])->columns(3),
                    
                Forms\Components\Section::make('Logo')
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->image()
                            ->directory('dealer-logos')
                            ->maxSize(2048),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->circular()
                    ->defaultImageUrl(fn () => 'https://ui-avatars.com/api/?name=D'),
                Tables\Columns\TextColumn::make('company_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Owner')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'authorized' => 'success',
                        'franchise' => 'info',
                        default => 'gray',
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('city')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_verified')
                    ->boolean()
                    ->label('Verified'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                Tables\Columns\TextColumn::make('rating')
                    ->numeric(decimalPlaces: 1)
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_purchases')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_verified')
                    ->label('Verification')
                    ->placeholder('All')
                    ->trueLabel('Verified only')
                    ->falseLabel('Pending'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('All')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive'),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'independent' => 'Independent',
                        'franchise' => 'Franchise',
                        'authorized' => 'Authorized',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('verify')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Dealer $record) => !$record->is_verified)
                    ->action(fn (Dealer $record) => $record->update([
                        'is_verified' => true,
                        'verified_at' => now(),
                    ])),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDealers::route('/'),
            'create' => Pages\CreateDealer::route('/create'),
            'view' => Pages\ViewDealer::route('/{record}'),
            'edit' => Pages\EditDealer::route('/{record}/edit'),
        ];
    }
}
