<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConversationResource\Pages;
use App\Filament\Resources\ConversationResource\RelationManagers;
use App\Models\Conversation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ConversationResource extends Resource
{
    protected static ?string $model = Conversation::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Messages';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereDate('last_message_at', '>=', now()->subDay())->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Conversation Details')
                    ->schema([
                        Forms\Components\Select::make('buyer_id')
                            ->relationship('buyer', 'name')
                            ->label('Buyer')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('seller_id')
                            ->relationship('seller', 'name')
                            ->label('Seller')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('vehicle_id')
                            ->relationship('vehicle', 'title')
                            ->label('Vehicle')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Forms\Components\Textarea::make('last_message')
                            ->label('Last Message')
                            ->disabled()
                            ->rows(2)
                            ->columnSpanFull(),
                        Forms\Components\DateTimePicker::make('last_message_at')
                            ->label('Last Message At')
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Participants')
                    ->schema([
                        Infolists\Components\TextEntry::make('buyer.name')
                            ->label('Buyer'),
                        Infolists\Components\TextEntry::make('buyer.email')
                            ->label('Buyer Email')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('seller.name')
                            ->label('Seller'),
                        Infolists\Components\TextEntry::make('seller.email')
                            ->label('Seller Email')
                            ->copyable(),
                    ])->columns(2),

                Infolists\Components\Section::make('Context')
                    ->schema([
                        Infolists\Components\TextEntry::make('vehicle.title')
                            ->label('Vehicle'),
                        Infolists\Components\TextEntry::make('last_message')
                            ->label('Last Message'),
                        Infolists\Components\TextEntry::make('last_message_at')
                            ->label('Last Activity')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('messages_count')
                            ->label('Total Messages')
                            ->state(fn (Conversation $record): int => $record->messages()->count()),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Started')
                            ->dateTime(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('buyer.name')
                    ->label('Buyer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('seller.name')
                    ->label('Seller')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('vehicle.title')
                    ->label('Vehicle')
                    ->searchable()
                    ->limit(25)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('last_message')
                    ->label('Last Message')
                    ->limit(40),
                Tables\Columns\TextColumn::make('messages_count')
                    ->label('Messages')
                    ->counts('messages')
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_message_at')
                    ->label('Last Activity')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('active_today')
                    ->label('Active Today')
                    ->query(fn ($query) => $query->whereDate('last_message_at', today())),
                Tables\Filters\Filter::make('active_week')
                    ->label('Active This Week')
                    ->query(fn ($query) => $query->where('last_message_at', '>=', now()->subWeek())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalDescription('This will permanently delete the conversation and all its messages.'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('last_message_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MessagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConversations::route('/'),
            'view' => Pages\ViewConversation::route('/{record}'),
        ];
    }
}
