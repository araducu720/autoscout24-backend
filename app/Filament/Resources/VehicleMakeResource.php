<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleMakeResource\Pages;
use App\Filament\Resources\VehicleMakeResource\RelationManagers;
use App\Models\VehicleMake;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VehicleMakeResource extends Resource
{
    protected static ?string $model = VehicleMake::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    
    protected static ?string $navigationGroup = 'Vehicle Management';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => 
                        $operation === 'create' ? $set('slug', \Illuminate\Support\Str::slug($state)) : null
                    ),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\FileUpload::make('logo')
                    ->image()
                    ->directory('vehicle-makes'),
                Forms\Components\Select::make('type')
                    ->required()
                    ->options([
                        'car' => 'Car',
                        'motorcycle' => 'Motorcycle',
                        'truck' => 'Truck',
                        'caravan' => 'Caravan',
                    ])
                    ->default('car'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'car' => 'primary',
                        'motorcycle' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('models_count')
                    ->counts('models')
                    ->label('Models'),
                Tables\Columns\TextColumn::make('vehicles_count')
                    ->counts('vehicles')
                    ->label('Vehicles'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'car' => 'Car',
                        'motorcycle' => 'Motorcycle',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListVehicleMakes::route('/'),
            'create' => Pages\CreateVehicleMake::route('/create'),
            'edit' => Pages\EditVehicleMake::route('/{record}/edit'),
        ];
    }
}
