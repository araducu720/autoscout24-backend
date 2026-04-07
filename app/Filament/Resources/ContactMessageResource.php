<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use App\Notifications\ContactMessageReplyNotification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    
    protected static ?string $navigationGroup = 'Messages';
    
    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'new')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Message Details')
                    ->schema([
                        Forms\Components\Select::make('vehicle_id')
                            ->relationship('vehicle', 'title')
                            ->required()
                            ->searchable()
                            ->preload(),
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
                        Forms\Components\Textarea::make('message')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('status')
                            ->required()
                            ->options([
                                'new' => 'New',
                                'read' => 'Read',
                                'replied' => 'Replied',
                            ])
                            ->default('new'),
                    ])->columns(2),

                Forms\Components\Section::make('Admin Reply')
                    ->schema([
                        Forms\Components\TextInput::make('reply_subject')
                            ->label('Reply Subject')
                            ->disabled(),
                        Forms\Components\Textarea::make('admin_reply')
                            ->label('Reply Content')
                            ->rows(4)
                            ->disabled()
                            ->columnSpanFull(),
                        Forms\Components\Placeholder::make('replied_info')
                            ->label('Replied')
                            ->content(fn (ContactMessage $record): string => 
                                $record->replied_at 
                                    ? $record->replied_at->format('d.m.Y H:i') . ' by ' . ($record->repliedByUser?->name ?? 'Unknown')
                                    : 'Not yet replied'
                            ),
                    ])
                    ->visible(fn (?ContactMessage $record) => $record && $record->replied_at)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vehicle.title')
                    ->sortable()
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'warning',
                        'read' => 'info',
                        'replied' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('replied_at')
                    ->label('Replied At')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'new' => 'New',
                        'read' => 'Read',
                        'replied' => 'Replied',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('mark_read')
                    ->icon('heroicon-o-envelope-open')
                    ->color('info')
                    ->visible(fn (ContactMessage $record) => $record->status === 'new')
                    ->action(function (ContactMessage $record) {
                        $record->update(['status' => 'read']);
                        Notification::make()
                            ->title('Marked as read')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('reply')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->visible(fn (ContactMessage $record) => in_array($record->status, ['new', 'read']))
                    ->form([
                        Forms\Components\Placeholder::make('original_message')
                            ->label('Original Message')
                            ->content(fn (ContactMessage $record): string => 
                                "From: {$record->name} ({$record->email})\n" .
                                "Vehicle: " . ($record->vehicle?->title ?? 'N/A') . "\n\n" .
                                $record->message
                            ),
                        Forms\Components\TextInput::make('reply_subject')
                            ->label('Subject')
                            ->required()
                            ->default(fn (ContactMessage $record): string => 
                                'Re: Your inquiry about ' . ($record->vehicle?->title ?? 'a vehicle')
                            )
                            ->maxLength(255),
                        Forms\Components\Textarea::make('reply_body')
                            ->label('Reply Message')
                            ->required()
                            ->rows(6)
                            ->placeholder('Write your reply to the customer here...')
                            ->default(fn (ContactMessage $record): string => 
                                "Dear {$record->name},\n\nThank you for your interest.\n\n"
                            ),
                    ])
                    ->action(function (ContactMessage $record, array $data) {
                        // Send email notification to customer
                        NotificationFacade::route('mail', $record->email)
                            ->notify(new ContactMessageReplyNotification(
                                $record,
                                $data['reply_subject'],
                                $data['reply_body'],
                            ));

                        // Update the record
                        $record->update([
                            'status' => 'replied',
                            'admin_reply' => $data['reply_body'],
                            'reply_subject' => $data['reply_subject'],
                            'replied_at' => now(),
                            'replied_by' => auth()->id(),
                        ]);

                        Notification::make()
                            ->title('Reply sent successfully')
                            ->body("Email sent to {$record->email}")
                            ->success()
                            ->send();
                    })
                    ->modalHeading('Reply to Contact Message')
                    ->modalSubmitActionLabel('Send Reply')
                    ->modalWidth('lg'),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactMessages::route('/'),
            'create' => Pages\CreateContactMessage::route('/create'),
            'view' => Pages\ViewContactMessage::route('/{record}'),
            'edit' => Pages\EditContactMessage::route('/{record}/edit'),
        ];
    }
}
