<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestDriveRequestResource\Pages;
use App\Models\TestDriveRequest;
use App\Notifications\TestDriveConfirmedNotification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class TestDriveRequestResource extends Resource
{
    protected static ?string $model = TestDriveRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    
    protected static ?string $navigationGroup = 'Messages';
    
    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Test Drive Requests';

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
                Forms\Components\Section::make('Request Details')
                    ->schema([
                        Forms\Components\Select::make('vehicle_id')
                            ->relationship('vehicle', 'title')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Guest request'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->default('pending'),
                    ])->columns(3),

                Forms\Components\Section::make('Contact Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),
                    ])->columns(3),

                Forms\Components\Section::make('Schedule')
                    ->schema([
                        Forms\Components\DatePicker::make('preferred_date')
                            ->required(),
                        Forms\Components\TimePicker::make('preferred_time'),
                    ])->columns(2),

                Forms\Components\Section::make('Message')
                    ->schema([
                        Forms\Components\Textarea::make('message')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vehicle.title')
                    ->label('Vehicle')
                    ->limit(25)
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('phone')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('preferred_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('preferred_time')
                    ->time()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        'no_show' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'no_show' => 'No Show',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('confirm')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (TestDriveRequest $record) => $record->status === 'pending')
                    ->action(function (TestDriveRequest $record) {
                        $record->update(['status' => 'confirmed']);
                        
                        // Send confirmation email to the requester
                        NotificationFacade::route('mail', $record->email)
                            ->notify(new TestDriveConfirmedNotification($record));
                        
                        Notification::make()
                            ->title('Test drive confirmed')
                            ->body("Confirmation email sent to {$record->email}")
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('cancel')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (TestDriveRequest $record) => in_array($record->status, ['pending', 'confirmed']))
                    ->action(function (TestDriveRequest $record) {
                        $record->update(['status' => 'cancelled']);
                        Notification::make()->title('Test drive cancelled')->warning()->send();
                    }),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('preferred_date', 'asc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTestDriveRequests::route('/'),
            'create' => Pages\CreateTestDriveRequest::route('/create'),
            'view' => Pages\ViewTestDriveRequest::route('/{record}'),
            'edit' => Pages\EditTestDriveRequest::route('/{record}/edit'),
        ];
    }
}
