<?php

namespace App\Filament\Resources\SafetradeTransactionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TimelineRelationManager extends RelationManager
{
    protected static string $relationship = 'timeline';

    protected static ?string $title = 'Transaction Timeline';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('event')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->rows(2),
                Forms\Components\TextInput::make('actor_name'),
                Forms\Components\TextInput::make('actor_role'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('event')
            ->columns([
                Tables\Columns\TextColumn::make('event')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->wrap(),
                Tables\Columns\TextColumn::make('actor_name')
                    ->label('Actor'),
                Tables\Columns\TextColumn::make('actor_role')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'buyer' => 'info',
                        'seller' => 'success',
                        'admin' => 'danger',
                        'system' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('timestamp')
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Event')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['actor_id'] = auth()->id();
                        $data['actor_name'] = auth()->user()->name;
                        $data['actor_role'] = 'admin';
                        $data['timestamp'] = now();
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('timestamp', 'desc');
    }
}
