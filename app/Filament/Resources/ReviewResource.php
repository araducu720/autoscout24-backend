<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 5;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Review Details')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Reviewer')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('vehicle_id')
                            ->relationship('vehicle', 'title')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('transaction_id')
                            ->relationship('transaction', 'id')
                            ->label('Transaction')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Forms\Components\Select::make('status')
                            ->required()
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->default('pending'),
                    ])->columns(2),

                Forms\Components\Section::make('Ratings')
                    ->schema([
                        Forms\Components\TextInput::make('rating')
                            ->label('Overall Rating')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(5)
                            ->step(1),
                        Forms\Components\TextInput::make('rating_vehicle')
                            ->label('Vehicle Rating')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(5)
                            ->step(1),
                        Forms\Components\TextInput::make('rating_seller')
                            ->label('Seller Rating')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(5)
                            ->step(1),
                        Forms\Components\TextInput::make('rating_shipping')
                            ->label('Shipping Rating')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(5)
                            ->step(1),
                    ])->columns(4),

                Forms\Components\Section::make('Content')
                    ->schema([
                        Forms\Components\Textarea::make('comment')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('anonymous')
                            ->label('Anonymous Review')
                            ->helperText('Hide reviewer identity from public view'),
                        Forms\Components\TextInput::make('helpful_count')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Reviewer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('vehicle.title')
                    ->label('Vehicle')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('rating')
                    ->label('Rating')
                    ->sortable()
                    ->formatStateUsing(fn (int $state): string => str_repeat('★', $state) . str_repeat('☆', 5 - $state)),
                Tables\Columns\TextColumn::make('comment')
                    ->limit(50)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('anonymous')
                    ->boolean()
                    ->label('Anon')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('helpful_count')
                    ->label('Helpful')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                Tables\Filters\SelectFilter::make('rating')
                    ->options([
                        '5' => '5 Stars',
                        '4' => '4 Stars',
                        '3' => '3 Stars',
                        '2' => '2 Stars',
                        '1' => '1 Star',
                    ]),
                Tables\Filters\TernaryFilter::make('anonymous')
                    ->label('Anonymous'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Review $record): bool => $record->status !== 'approved')
                    ->action(function (Review $record): void {
                        $record->update(['status' => 'approved']);
                        Notification::make()
                            ->title('Review approved')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Review $record): bool => $record->status !== 'rejected')
                    ->action(function (Review $record): void {
                        $record->update(['status' => 'rejected']);
                        Notification::make()
                            ->title('Review rejected')
                            ->warning()
                            ->send();
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_approve')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['status' => 'approved']))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('bulk_reject')
                        ->label('Reject Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['status' => 'rejected']))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'view' => Pages\ViewReview::route('/{record}'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}
