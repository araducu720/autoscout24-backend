<?php

namespace App\Filament\Widgets;

use App\Models\SafetradeTransaction;
use App\Filament\Resources\SafetradeTransactionResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class PaymentVerificationQueue extends BaseWidget
{
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 'full';

    protected function getTableHeading(): string
    {
        return 'Pending Escrow Payments';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                SafetradeTransaction::query()
                    ->where(function ($q) {
                        $q->where('escrow_status', 'pending')
                          ->orWhere('escrow_status', 'awaiting_verification');
                    })
                    ->whereNotIn('status', ['completed', 'cancelled', 'refunded'])
                    ->with(['buyer', 'seller'])
                    ->orderBy('created_at', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('vehicle_title')
                    ->label('Vehicle')
                    ->limit(25),
                Tables\Columns\TextColumn::make('vehicle_price')
                    ->label('Price')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('escrow_fee')
                    ->label('Fee')
                    ->money('EUR'),
                Tables\Columns\TextColumn::make('payment_method')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'bank_transfer' => 'primary',
                        'credit_card' => 'info',
                        'escrow' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('buyer.name')
                    ->label('Buyer'),
                Tables\Columns\TextColumn::make('seller.name')
                    ->label('Seller'),
                Tables\Columns\TextColumn::make('escrow_status')
                    ->label('Escrow')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'pending' => 'warning',
                        'awaiting_verification' => 'info',
                        'funded' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->actions([
                Tables\Actions\Action::make('view_proof')
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->label('Proof')
                    ->visible(fn (SafetradeTransaction $record) => !empty($record->payment_proof_path))
                    ->url(fn (SafetradeTransaction $record) => Storage::url($record->payment_proof_path), shouldOpenInNewTab: true),
                Tables\Actions\Action::make('view')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn (SafetradeTransaction $record) => SafetradeTransactionResource::getUrl('view', ['record' => $record])),
                Tables\Actions\Action::make('mark_funded')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Confirm Payment Received')
                    ->modalDescription('Has the bank transfer been received and verified against the payment proof? This will mark the escrow as funded and notify the seller.')
                    ->action(function (SafetradeTransaction $record) {
                        // Fund the transaction properly
                        $record->fund('bank_transfer');

                        // Update escrow record
                        if ($escrow = $record->escrow) {
                            $conditions = $escrow->release_conditions ?? [];
                            $conditions['payment_verified'] = true;
                            $escrow->update([
                                'status' => 'funded',
                                'funded_at' => now(),
                                'release_conditions' => $conditions,
                            ]);
                        }

                        // Update invoice
                        if ($record->invoice) {
                            $record->invoice->update(['status' => 'paid']);
                        }

                        // Timeline
                        $record->addTimelineEvent(
                            'payment_verified',
                            'Admin verified bank transfer payment. Escrow funded.',
                            auth()->id(),
                            auth()->user()->name ?? 'Admin',
                            'admin'
                        );

                        Notification::make()
                            ->title('Payment Verified & Funded')
                            ->body("Transaction {$record->reference} marked as funded.")
                            ->success()
                            ->send();
                    }),
            ])
            ->emptyStateHeading('No pending payments')
            ->emptyStateDescription('All escrow payments have been processed.')
            ->emptyStateIcon('heroicon-o-check-badge')
            ->poll('30s');
    }
}
