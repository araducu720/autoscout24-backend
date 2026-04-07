<?php

namespace App\Filament\Resources\ConversationResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class MessagesRelationManager extends RelationManager
{
    protected static string $relationship = 'messages';

    protected static ?string $title = 'Messages';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sender.name')
                    ->label('Sender')
                    ->sortable(),
                Tables\Columns\TextColumn::make('content')
                    ->label('Message')
                    ->limit(80)
                    ->wrap(),
                Tables\Columns\IconColumn::make('has_attachments')
                    ->label('Files')
                    ->state(fn ($record): bool => !empty($record->attachments))
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('read_at')
                    ->label('Read')
                    ->dateTime()
                    ->placeholder('Unread')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Sent')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'asc');
    }
}
