<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationGroup = 'Safe Trade';
    
    protected static ?int $navigationSort = 9;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Invoice Details')
                    ->schema([
                        Forms\Components\TextInput::make('invoice_number')
                            ->disabled(),
                        Forms\Components\Select::make('safetrade_transaction_id')
                            ->relationship('safetradeTransaction', 'reference')
                            ->disabled(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'issued' => 'Issued',
                                'paid' => 'Paid',
                                'overdue' => 'Overdue',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required(),
                    ])->columns(3),

                Forms\Components\Section::make('Amounts')
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->label('Vehicle Amount')
                            ->prefix('€')
                            ->numeric()
                            ->disabled(),
                        Forms\Components\TextInput::make('escrow_fee')
                            ->prefix('€')
                            ->numeric()
                            ->disabled(),
                        Forms\Components\TextInput::make('total')
                            ->prefix('€')
                            ->numeric()
                            ->disabled(),
                    ])->columns(3),

                Forms\Components\Section::make('Dates')
                    ->schema([
                        Forms\Components\DatePicker::make('issue_date'),
                        Forms\Components\DatePicker::make('due_date'),
                    ])->columns(2),

                Forms\Components\Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('safetradeTransaction.reference')
                    ->label('Transaction')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('escrow_fee')
                    ->money('EUR')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('total')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'issued' => 'info',
                        'paid' => 'success',
                        'overdue' => 'danger',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('issue_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'issued' => 'Issued',
                        'paid' => 'Paid',
                        'overdue' => 'Overdue',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
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
            'index' => Pages\ListInvoices::route('/'),
            'view' => Pages\ViewInvoice::route('/{record}'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
