<?php

namespace App\Filament\Resources\VehicleResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    protected static ?string $title = 'Vehicle Images';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('image_path')
                    ->label('Image URL or Path')
                    ->required()
                    ->maxLength(2000)
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_primary')
                    ->label('Primary Image')
                    ->default(false),
                Forms\Components\TextInput::make('order')
                    ->numeric()
                    ->default(0)
                    ->label('Display Order'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('image_path')
                    ->label('Image Path')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_primary')
                    ->boolean()
                    ->label('Primary'),
                Tables\Columns\TextColumn::make('order')
                    ->sortable()
                    ->label('Order'),
            ])
            ->defaultSort('order')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Image'),
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
}
