<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EscrowTransactionResource\Pages;
use App\Models\EscrowTransaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class EscrowTransactionResource extends Resource
{
    protected static ?string $model = EscrowTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';
    
    protected static ?string $navigationGroup = 'Safe Trade';
    
    protected static ?int $navigationSort = 7;

    protected static ?string $navigationLabel = 'Escrow Accounts';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereIn('status', ['funded'])->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Escrow Details')
                    ->schema([
                        Forms\Components\Select::make('safetrade_transaction_id')
                            ->relationship('safetradeTransaction', 'reference')
                            ->disabled(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'funded' => 'Funded',
                                'released' => 'Released',
                                'refunded' => 'Refunded',
                                'disputed' => 'Disputed',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('amount')
                            ->prefix('€')
                            ->numeric()
                            ->disabled(),
                        Forms\Components\TextInput::make('payment_reference')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('escrow_iban')
                            ->label('Escrow IBAN')
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Parties')
                    ->schema([
                        Forms\Components\Select::make('buyer_id')
                            ->relationship('buyer', 'name')
                            ->disabled(),
                        Forms\Components\Select::make('seller_id')
                            ->relationship('seller', 'name')
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Release Conditions')
                    ->schema([
                        Forms\Components\TagsInput::make('release_conditions')
                            ->placeholder('Add condition...')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Dispute')
                    ->schema([
                        Forms\Components\Textarea::make('dispute_reason')
                            ->rows(3),
                    ])
                    ->visible(fn ($record) => $record?->status === 'disputed')
                    ->collapsible(),

                Forms\Components\Section::make('Timestamps')
                    ->schema([
                        Forms\Components\DateTimePicker::make('funded_at')->disabled(),
                        Forms\Components\DateTimePicker::make('released_at')->disabled(),
                        Forms\Components\DateTimePicker::make('refunded_at')->disabled(),
                    ])->columns(3)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('safetradeTransaction.reference')
                    ->label('Transaction')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('buyer.name')
                    ->label('Buyer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('seller.name')
                    ->label('Seller')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'funded' => 'success',
                        'released' => 'success',
                        'refunded' => 'danger',
                        'disputed' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('funded_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('released_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'funded' => 'Funded',
                        'released' => 'Released',
                        'refunded' => 'Refunded',
                        'disputed' => 'Disputed',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('release')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Release Escrow')
                    ->modalDescription('Release funds to the seller. This will also complete the SafeTrade transaction and order. This action cannot be undone.')
                    ->visible(fn (EscrowTransaction $record) => $record->status === 'funded')
                    ->action(function (EscrowTransaction $record) {
                        // Use parent SafetradeTransaction's releaseFunds() to keep both tables in sync
                        if ($parent = $record->safetradeTransaction) {
                            $parent->releaseFunds();

                            // Timeline entry
                            $parent->addTimelineEvent(
                                'funds_released',
                                'Admin released escrow funds to seller via Escrow Accounts panel.',
                                auth()->id(),
                                auth()->user()->name ?? 'Admin',
                                'admin'
                            );
                        } else {
                            // Fallback if no parent (shouldn't happen)
                            $record->update([
                                'status' => 'released',
                                'released_at' => now(),
                            ]);
                        }
                        Notification::make()->title('Escrow released & transaction completed')->success()->send();
                    }),
                Tables\Actions\Action::make('refund')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Refund Escrow')
                    ->modalDescription('Refund funds to the buyer. This will also cancel the SafeTrade transaction. This action cannot be undone.')
                    ->visible(fn (EscrowTransaction $record) => in_array($record->status, ['funded', 'disputed']))
                    ->action(function (EscrowTransaction $record) {
                        // Use parent SafetradeTransaction's refund() to keep both tables in sync
                        if ($parent = $record->safetradeTransaction) {
                            $parent->refund();

                            // Timeline entry
                            $parent->addTimelineEvent(
                                'funds_refunded',
                                'Admin refunded escrow to buyer via Escrow Accounts panel.',
                                auth()->id(),
                                auth()->user()->name ?? 'Admin',
                                'admin'
                            );
                        } else {
                            $record->update([
                                'status' => 'refunded',
                                'refunded_at' => now(),
                            ]);
                        }
                        Notification::make()->title('Escrow refunded & transaction cancelled')->success()->send();
                    }),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEscrowTransactions::route('/'),
            'view' => Pages\ViewEscrowTransaction::route('/{record}'),
            'edit' => Pages\EditEscrowTransaction::route('/{record}/edit'),
        ];
    }
}
