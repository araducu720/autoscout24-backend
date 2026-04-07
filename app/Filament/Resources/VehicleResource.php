<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleResource\Pages;
use App\Filament\Resources\VehicleResource\RelationManagers;
use App\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    
    protected static ?string $navigationGroup = 'Vehicle Management';
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\Select::make('make_id')
                            ->relationship('make', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live(),
                        Forms\Components\Select::make('model_id')
                            ->relationship('model', 'name', fn (Builder $query, Forms\Get $get) => 
                                $query->where('make_id', $get('make_id'))
                            )
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('description')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Pricing & Condition')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('€')
                            ->maxValue(999999.99),
                        Forms\Components\TextInput::make('year')
                            ->required()
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(date('Y') + 1),
                        Forms\Components\TextInput::make('mileage')
                            ->required()
                            ->numeric()
                            ->suffix('km'),
                        Forms\Components\Select::make('condition')
                            ->required()
                            ->options([
                                'new' => 'New',
                                'used' => 'Used',
                            ])
                            ->default('used'),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Technical Details')
                    ->schema([
                        Forms\Components\Select::make('fuel_type')
                            ->required()
                            ->options([
                                'petrol' => 'Petrol',
                                'diesel' => 'Diesel',
                                'electric' => 'Electric',
                                'hybrid' => 'Hybrid',
                                'lpg' => 'LPG',
                            ]),
                        Forms\Components\Select::make('transmission')
                            ->required()
                            ->options([
                                'manual' => 'Manual',
                                'automatic' => 'Automatic',
                            ]),
                        Forms\Components\Select::make('body_type')
                            ->options([
                                'sedan' => 'Sedan',
                                'suv' => 'SUV',
                                'coupe' => 'Coupe',
                                'hatchback' => 'Hatchback',
                                'wagon' => 'Wagon',
                                'convertible' => 'Convertible',
                                'van' => 'Van',
                                'pickup' => 'Pickup',
                            ]),
                        Forms\Components\TextInput::make('color')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('doors')
                            ->numeric()
                            ->minValue(2)
                            ->maxValue(5),
                        Forms\Components\TextInput::make('seats')
                            ->numeric()
                            ->minValue(2)
                            ->maxValue(9),
                        Forms\Components\TextInput::make('engine_size')
                            ->numeric()
                            ->suffix('cc'),
                        Forms\Components\TextInput::make('power')
                            ->numeric()
                            ->suffix('hp'),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Location')
                    ->schema([
                        Forms\Components\TextInput::make('country')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('city')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Media')
                    ->schema([
                        Forms\Components\TextInput::make('video_url')
                            ->label('Video URL')
                            ->url()
                            ->maxLength(500)
                            ->placeholder('https://www.youtube.com/watch?v=...')
                            ->helperText('YouTube or Vimeo video URL')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Features')
                    ->schema([
                        Forms\Components\TagsInput::make('features')
                            ->placeholder('Add feature...')
                            ->helperText('Type a feature and press Enter')
                            ->columnSpanFull(),
                    ])->collapsible(),
                    
                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Owner / Seller')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Forms\Components\Select::make('status')
                            ->required()
                            ->options([
                                'active' => 'Active',
                                'sold' => 'Sold',
                                'inactive' => 'Inactive',
                            ])
                            ->default('active'),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Featured Vehicle'),
                        Forms\Components\TextInput::make('views_count')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('primaryImage.image_path')
                    ->label('Image')
                    ->disk('public')
                    ->square()
                    ->size(50)
                    ->defaultImageUrl(fn () => 'https://placehold.co/50x50/e2e8f0/94a3b8?text=No+Img'),
                Tables\Columns\TextColumn::make('make.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('model.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('price')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('year')
                    ->sortable(),
                Tables\Columns\TextColumn::make('mileage')
                    ->numeric()
                    ->suffix(' km')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'sold' => 'danger',
                        'inactive' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->label('Featured'),
                Tables\Columns\TextColumn::make('views_count')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('make')
                    ->relationship('make', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'sold' => 'Sold',
                        'inactive' => 'Inactive',
                    ]),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured')
                    ->placeholder('All vehicles')
                    ->trueLabel('Featured only')
                    ->falseLabel('Not featured'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_feature')
                        ->label('Mark as Featured')
                        ->icon('heroicon-s-star')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['is_featured' => true]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('bulk_unfeature')
                        ->label('Remove Featured')
                        ->icon('heroicon-o-star')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['is_featured' => false]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('bulk_activate')
                        ->label('Set Active')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['status' => 'active']))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('bulk_deactivate')
                        ->label('Set Inactive')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['status' => 'inactive']))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ImagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            'view' => Pages\ViewVehicle::route('/{record}'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }
}
