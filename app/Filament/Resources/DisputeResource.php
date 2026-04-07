<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DisputeResource\Pages;
use App\Models\Dispute;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class DisputeResource extends Resource
{
    protected static ?string $model = Dispute::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';
    
    protected static ?string $navigationGroup = 'Safe Trade';
    
    protected static ?int $navigationSort = 5;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::open()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::open()->count() > 0 ? 'danger' : 'primary';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Dispute Information')
                    ->schema([
                        Forms\Components\TextInput::make('reference')
                            ->disabled(),
                        Forms\Components\Select::make('type')
                            ->options(Dispute::TYPE_LABELS)
                            ->disabled(),
                        Forms\Components\Select::make('status')
                            ->options(Dispute::STATUS_LABELS)
                            ->required(),
                        Forms\Components\Select::make('priority')
                            ->options(Dispute::PRIORITY_LABELS)
                            ->required(),
                    ])->columns(4),

                Forms\Components\Section::make('Transaction Details')
                    ->schema([
                        Forms\Components\Placeholder::make('transaction_ref')
                            ->label('Transaction Reference')
                            ->content(fn ($record) => $record?->transaction?->reference ?? '-'),
                        Forms\Components\Placeholder::make('vehicle')
                            ->label('Vehicle')
                            ->content(fn ($record) => $record?->transaction?->vehicle_title ?? $record?->transaction?->vehicle?->title ?? '-'),
                        Forms\Components\Placeholder::make('amount')
                            ->label('Amount')
                            ->content(fn ($record) => $record?->transaction ? '€' . number_format($record->transaction->amount, 2) : '-'),
                        Forms\Components\Placeholder::make('buyer')
                            ->label('Buyer')
                            ->content(fn ($record) => $record?->transaction?->buyer?->name ?? '-'),
                        Forms\Components\Placeholder::make('seller')
                            ->label('Seller')
                            ->content(fn ($record) => $record?->transaction?->seller?->name ?? '-'),
                        Forms\Components\Placeholder::make('opened_by_user')
                            ->label('Opened By')
                            ->content(fn ($record) => $record?->openedBy?->name ?? '-'),
                    ])->columns(3),

                Forms\Components\Section::make('Description')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->rows(4)
                            ->disabled(),
                    ]),

                Forms\Components\Section::make('Assignment')
                    ->schema([
                        Forms\Components\Select::make('assigned_to')
                            ->label('Assign To')
                            ->options(User::where('is_admin', true)->pluck('name', 'id'))
                            ->searchable(),
                        Forms\Components\Placeholder::make('assigned_at_display')
                            ->label('Assigned At')
                            ->content(fn ($record) => $record?->assigned_at?->format('M d, Y H:i') ?? 'Not assigned'),
                    ])->columns(2),

                Forms\Components\Section::make('Resolution')
                    ->schema([
                        Forms\Components\Select::make('resolution_outcome')
                            ->options(Dispute::RESOLUTION_LABELS)
                            ->visible(fn ($record) => $record?->status === 'resolved'),
                        Forms\Components\Textarea::make('resolution_notes')
                            ->rows(3),
                        Forms\Components\Placeholder::make('resolved_by_display')
                            ->label('Resolved By')
                            ->content(fn ($record) => $record?->resolvedBy?->name ?? '-')
                            ->visible(fn ($record) => $record?->status === 'resolved'),
                        Forms\Components\Placeholder::make('resolved_at_display')
                            ->label('Resolved At')
                            ->content(fn ($record) => $record?->resolved_at?->format('M d, Y H:i') ?? '-')
                            ->visible(fn ($record) => $record?->status === 'resolved'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('transaction.reference')
                    ->label('Transaction')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->formatStateUsing(fn (string $state) => Dispute::TYPE_LABELS[$state] ?? $state)
                    ->wrap(),
                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'gray',
                        'medium' => 'info',
                        'high' => 'warning',
                        'urgent' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open' => 'danger',
                        'under_review' => 'warning',
                        'awaiting_info' => 'info',
                        'mediation' => 'primary',
                        'resolved' => 'success',
                        'closed' => 'gray',
                        'escalated' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('openedBy.name')
                    ->label('Opened By'),
                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label('Assigned To')
                    ->placeholder('Unassigned'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_activity_at')
                    ->label('Last Activity')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Dispute::STATUS_LABELS),
                Tables\Filters\SelectFilter::make('priority')
                    ->options(Dispute::PRIORITY_LABELS),
                Tables\Filters\SelectFilter::make('type')
                    ->options(Dispute::TYPE_LABELS),
                Tables\Filters\TernaryFilter::make('unassigned')
                    ->label('Unassigned Only')
                    ->queries(
                        true: fn ($query) => $query->whereNull('assigned_to'),
                        false: fn ($query) => $query->whereNotNull('assigned_to'),
                    ),
            ])
            ->actions([
                Tables\Actions\Action::make('assign_to_me')
                    ->icon('heroicon-o-user-plus')
                    ->color('primary')
                    ->visible(fn (Dispute $record) => $record->assigned_to === null)
                    ->action(function (Dispute $record) {
                        $record->assignTo(auth()->id());
                        Notification::make()
                            ->title('Dispute assigned to you')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('resolve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Select::make('resolution_outcome')
                            ->label('Resolution')
                            ->options(Dispute::RESOLUTION_LABELS)
                            ->required(),
                        Forms\Components\Textarea::make('resolution_notes')
                            ->label('Notes')
                            ->required()
                            ->rows(3),
                    ])
                    ->visible(fn (Dispute $record) => $record->isOpen())
                    ->action(function (Dispute $record, array $data) {
                        $record->resolve($data['resolution_outcome'], $data['resolution_notes'], auth()->id());
                        Notification::make()
                            ->title('Dispute resolved')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('escalate')
                    ->icon('heroicon-o-arrow-up-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Escalation Reason')
                            ->required()
                            ->rows(2),
                    ])
                    ->visible(fn (Dispute $record) => $record->isOpen() && $record->status !== 'escalated')
                    ->action(function (Dispute $record, array $data) {
                        $record->escalate(auth()->id(), $data['reason']);
                        Notification::make()
                            ->title('Dispute escalated')
                            ->warning()
                            ->send();
                    }),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('assign_to_me')
                    ->icon('heroicon-o-user-plus')
                    ->action(function ($records) {
                        $records->each(fn ($record) => $record->assignTo(auth()->id()));
                        Notification::make()
                            ->title('Disputes assigned to you')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            DisputeResource\RelationManagers\MessagesRelationManager::class,
            DisputeResource\RelationManagers\AttachmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDisputes::route('/'),
            'create' => Pages\CreateDispute::route('/create'),
            'view' => Pages\ViewDispute::route('/{record}'),
            'edit' => Pages\EditDispute::route('/{record}/edit'),
        ];
    }
}
