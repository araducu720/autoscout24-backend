<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SafetradeTransactionResource\Pages;
use App\Filament\Resources\SafetradeTransactionResource\RelationManagers;
use App\Models\SafetradeTransaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class SafetradeTransactionResource extends Resource
{
    protected static ?string $model = SafetradeTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    
    protected static ?string $navigationGroup = 'Safe Trade';
    
    protected static ?int $navigationSort = 6;

    protected static ?string $navigationLabel = 'SafeTrade Transactions';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereIn('status', ['pending', 'payment_uploaded'])->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::whereIn('status', ['pending', 'payment_uploaded'])->count();

        return $count > 0 ? 'warning' : 'info';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Transaction Details')
                    ->schema([
                        Forms\Components\TextInput::make('reference')
                            ->disabled(),
                        Forms\Components\Select::make('order_id')
                            ->relationship('order', 'order_number')
                            ->disabled(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'payment_uploaded' => 'Payment Uploaded',
                                'confirmed' => 'Confirmed',
                                'in_transit' => 'In Transit',
                                'delivered' => 'Delivered',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                                'disputed' => 'Disputed',
                            ])
                            ->required(),
                        Forms\Components\Select::make('escrow_status')
                            ->options([
                                'pending' => 'Pending',
                                'awaiting_verification' => 'Awaiting Verification',
                                'funded' => 'Funded',
                                'released' => 'Released',
                                'refunded' => 'Refunded',
                                'disputed' => 'Disputed',
                            ]),
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

                Forms\Components\Section::make('Vehicle & Pricing')
                    ->schema([
                        Forms\Components\Select::make('vehicle_id')
                            ->relationship('vehicle', 'title')
                            ->disabled(),
                        Forms\Components\TextInput::make('vehicle_title')
                            ->disabled(),
                        Forms\Components\TextInput::make('vehicle_price')
                            ->prefix('€')
                            ->disabled(),
                        Forms\Components\TextInput::make('escrow_fee')
                            ->prefix('€')
                            ->disabled(),
                        Forms\Components\TextInput::make('amount')
                            ->label('Total Amount')
                            ->prefix('€')
                            ->disabled(),
                    ])->columns(3),

                Forms\Components\Section::make('Payment & Delivery')
                    ->schema([
                        Forms\Components\Select::make('payment_method')
                            ->options([
                                'bank_transfer' => 'Bank Transfer',
                                'credit_card' => 'Credit Card',
                            ])
                            ->disabled(),
                        Forms\Components\Select::make('payment_status')
                            ->options([
                                'pending' => 'Pending',
                                'awaiting_verification' => 'Awaiting Verification',
                                'processing' => 'Processing',
                                'completed' => 'Completed',
                                'failed' => 'Failed',
                                'refunded' => 'Refunded',
                            ]),
                        Forms\Components\Select::make('delivery_method')
                            ->options([
                                'pickup' => 'Pickup',
                                'delivery' => 'Delivery',
                                'shipping' => 'Professional Shipping',
                            ]),
                        Forms\Components\TextInput::make('delivery_address'),
                        Forms\Components\TextInput::make('tracking_number'),
                    ])->columns(3),

                Forms\Components\Section::make('Payment Proof')
                    ->schema([
                        Forms\Components\Placeholder::make('payment_proof_preview')
                            ->label('Uploaded Proof')
                            ->content(function ($record) {
                                if (!$record || empty($record->payment_proof_path)) {
                                    return 'No payment proof uploaded yet.';
                                }
                                $url = \Illuminate\Support\Facades\Storage::url($record->payment_proof_path);
                                $ext = strtolower(pathinfo($record->payment_proof_path, PATHINFO_EXTENSION));
                                if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                                    return new \Illuminate\Support\HtmlString(
                                        '<a href="' . $url . '" target="_blank"><img src="' . $url . '" style="max-width:400px;max-height:300px;border-radius:8px;border:1px solid #e5e7eb;" /></a>'
                                        . '<br><a href="' . $url . '" target="_blank" class="text-primary-600 hover:underline text-sm mt-2 inline-block">Open full size ↗</a>'
                                    );
                                }
                                return new \Illuminate\Support\HtmlString(
                                    '<a href="' . $url . '" target="_blank" class="inline-flex items-center gap-2 text-primary-600 hover:underline">'
                                    . '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" /></svg>'
                                    . 'View PDF Payment Proof ↗</a>'
                                );
                            })
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('payment_proof_path')
                            ->label('Proof File Path')
                            ->disabled()
                            ->visible(fn ($record) => $record && !empty($record->payment_proof_path)),
                    ])
                    ->visible(fn ($record) => $record !== null)
                    ->collapsible(),

                Forms\Components\Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->rows(3),
                        Forms\Components\Textarea::make('cancellation_reason')
                            ->rows(2)
                            ->visible(fn ($record) => $record?->status === 'cancelled'),
                    ])->columns(2),

                Forms\Components\Section::make('Timestamps')
                    ->schema([
                        Forms\Components\DateTimePicker::make('funded_at')->disabled(),
                        Forms\Components\DateTimePicker::make('confirmed_at')->disabled(),
                        Forms\Components\DateTimePicker::make('delivered_at')->disabled(),
                        Forms\Components\DateTimePicker::make('completed_at')->disabled(),
                        Forms\Components\DateTimePicker::make('cancelled_at')->disabled(),
                    ])->columns(3)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('vehicle_title')
                    ->label('Vehicle')
                    ->limit(25)
                    ->searchable(),
                Tables\Columns\TextColumn::make('buyer.name')
                    ->label('Buyer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('seller.name')
                    ->label('Seller')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('escrow_fee')
                    ->money('EUR')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'payment_uploaded' => 'info',
                        'confirmed' => 'primary',
                        'in_transit' => 'info',
                        'delivered' => 'gray',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        'disputed' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('escrow_status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'pending' => 'warning',
                        'awaiting_verification' => 'info',
                        'funded' => 'success',
                        'released' => 'success',
                        'refunded' => 'danger',
                        'disputed' => 'danger',
                        default => 'gray',
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'payment_uploaded' => 'Payment Uploaded',
                        'confirmed' => 'Confirmed',
                        'in_transit' => 'In Transit',
                        'delivered' => 'Delivered',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'disputed' => 'Disputed',
                    ]),
                Tables\Filters\SelectFilter::make('escrow_status')
                    ->options([
                        'pending' => 'Pending',
                        'awaiting_verification' => 'Awaiting Verification',
                        'funded' => 'Funded',
                        'released' => 'Released',
                        'refunded' => 'Refunded',
                        'disputed' => 'Disputed',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('verify_payment')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Verify Payment & Fund Escrow')
                    ->modalDescription('Confirm that the bank transfer has been received. This will mark the escrow as funded and notify the seller to ship the vehicle.')
                    ->visible(fn (SafetradeTransaction $record) => in_array($record->escrow_status, ['pending', 'awaiting_verification']) && !empty($record->payment_proof_path))
                    ->action(function (SafetradeTransaction $record) {
                        $record->fund('bank_transfer');

                        if ($escrow = $record->escrow) {
                            $conditions = $escrow->release_conditions ?? [];
                            $conditions['payment_verified'] = true;
                            $escrow->update([
                                'status' => 'funded',
                                'funded_at' => now(),
                                'release_conditions' => $conditions,
                            ]);
                        }

                        if ($record->invoice) {
                            $record->invoice->update(['status' => 'paid']);
                        }

                        $record->addTimelineEvent(
                            'payment_verified',
                            'Admin verified bank transfer payment. Escrow funded.',
                            auth()->id(),
                            auth()->user()->name ?? 'Admin',
                            'admin'
                        );

                        Notification::make()
                            ->title('Payment verified & escrow funded')
                            ->body("Transaction {$record->reference} is now funded.")
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('release_escrow')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Release Escrow Funds')
                    ->modalDescription('Release escrow funds to the seller. This will complete the transaction and order. This action cannot be undone.')
                    ->visible(fn (SafetradeTransaction $record) => $record->escrow_status === 'funded' && in_array($record->status, ['delivered', 'confirmed']))
                    ->action(function (SafetradeTransaction $record) {
                        $record->releaseFunds();

                        $record->addTimelineEvent(
                            'funds_released',
                            'Admin released escrow funds to seller.',
                            auth()->id(),
                            auth()->user()->name ?? 'Admin',
                            'admin'
                        );

                        Notification::make()
                            ->title('Escrow funds released & transaction completed')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('refund')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Refund Transaction')
                    ->modalDescription('Refund escrow to the buyer. This will cancel the transaction. This action cannot be undone.')
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Refund Reason')
                            ->required()
                            ->rows(2),
                    ])
                    ->visible(fn (SafetradeTransaction $record) => in_array($record->escrow_status, ['funded', 'disputed']))
                    ->action(function (SafetradeTransaction $record, array $data) {
                        $record->refund();

                        $record->addTimelineEvent(
                            'funds_refunded',
                            'Admin refunded escrow to buyer. Reason: ' . $data['reason'],
                            auth()->id(),
                            auth()->user()->name ?? 'Admin',
                            'admin',
                            ['reason' => $data['reason']]
                        );

                        Notification::make()
                            ->title('Transaction refunded')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('view_proof')
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->label('Proof')
                    ->visible(fn (SafetradeTransaction $record) => !empty($record->payment_proof_path))
                    ->url(fn (SafetradeTransaction $record) => \Illuminate\Support\Facades\Storage::url($record->payment_proof_path), shouldOpenInNewTab: true),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Bulk delete removed for financial records - audit compliance
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TimelineRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSafetradeTransactions::route('/'),
            'view' => Pages\ViewSafetradeTransaction::route('/{record}'),
            'edit' => Pages\EditSafetradeTransaction::route('/{record}/edit'),
        ];
    }
}
