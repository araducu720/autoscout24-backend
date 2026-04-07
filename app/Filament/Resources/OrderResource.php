<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\SafetradeTransactionResource;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    
    protected static ?string $navigationGroup = 'Safe Trade';
    
    protected static ?int $navigationSort = 8;

    protected static ?string $navigationLabel = 'Purchase Orders';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereIn('status', ['pending', 'accepted'])->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Order Details')
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->disabled(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'accepted' => 'Accepted',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                                'rejected' => 'Rejected',
                            ])
                            ->disabled()
                            ->helperText('Order status is managed automatically by the SafeTrade flow.'),
                    ])->columns(2),

                Forms\Components\Section::make('Parties')
                    ->schema([
                        Forms\Components\Select::make('buyer_id')
                            ->relationship('buyer', 'name')
                            ->disabled(),
                        Forms\Components\Select::make('seller_id')
                            ->relationship('seller', 'name')
                            ->disabled(),
                        Forms\Components\Select::make('vehicle_id')
                            ->relationship('vehicle', 'title')
                            ->disabled(),
                    ])->columns(3),

                Forms\Components\Section::make('Pricing')
                    ->schema([
                        Forms\Components\TextInput::make('total_price')
                            ->prefix('€')
                            ->disabled(),
                        Forms\Components\TextInput::make('escrow_fee')
                            ->prefix('€')
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Delivery')
                    ->schema([
                        Forms\Components\Select::make('delivery_method')
                            ->options([
                                'pickup' => 'Pickup',
                                'delivery' => 'Delivery',
                                'shipping' => 'Professional Shipping',
                            ])
                            ->disabled(),
                        Forms\Components\TextInput::make('delivery_address')
                            ->disabled(),
                        Forms\Components\Textarea::make('message')
                            ->rows(2)
                            ->columnSpanFull()
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Linked SafeTrade Transaction')
                    ->schema([
                        Forms\Components\Placeholder::make('safetrade_link')
                            ->label('SafeTrade Transaction')
                            ->content(function ($record) {
                                if (!$record || !$record->safetradeTransaction) {
                                    return 'No linked SafeTrade transaction.';
                                }
                                $txn = $record->safetradeTransaction;
                                $url = SafetradeTransactionResource::getUrl('view', ['record' => $txn]);
                                return new \Illuminate\Support\HtmlString(
                                    '<a href="' . $url . '" class="text-primary-600 hover:underline font-medium">'
                                    . $txn->reference . ' — ' . ucfirst($txn->status)
                                    . '</a>'
                                );
                            }),
                    ])
                    ->visible(fn ($record) => $record !== null),

                Forms\Components\Section::make('Timestamps')
                    ->schema([
                        Forms\Components\DateTimePicker::make('payment_deadline')->disabled(),
                        Forms\Components\DateTimePicker::make('accepted_at')->disabled(),
                        Forms\Components\DateTimePicker::make('rejected_at')->disabled(),
                    ])->columns(3)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('vehicle.title')
                    ->label('Vehicle')
                    ->limit(25)
                    ->searchable(),
                Tables\Columns\TextColumn::make('buyer.name')
                    ->label('Buyer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('seller.name')
                    ->label('Seller')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('escrow_fee')
                    ->money('EUR')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'accepted' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('safetradeTransaction.reference')
                    ->label('SafeTrade Ref')
                    ->copyable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('delivery_method')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'accepted' => 'Accepted',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListOrders::route('/'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }
}
