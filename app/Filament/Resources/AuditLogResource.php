<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditLogResource\Pages;
use App\Models\AuditLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    
    protected static ?string $navigationGroup = 'System';
    
    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Audit Logs';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Audit Details')
                    ->schema([
                        Forms\Components\TextInput::make('action')
                            ->disabled(),
                        Forms\Components\TextInput::make('auditable_type')
                            ->label('Model Type')
                            ->disabled(),
                        Forms\Components\TextInput::make('auditable_id')
                            ->label('Model ID')
                            ->disabled(),
                        Forms\Components\Placeholder::make('user_name')
                            ->label('User')
                            ->content(fn ($record) => $record?->user?->name ?? 'System'),
                    ])->columns(2),

                Forms\Components\Section::make('Changes')
                    ->schema([
                        Forms\Components\KeyValue::make('old_values')
                            ->label('Old Values')
                            ->disabled(),
                        Forms\Components\KeyValue::make('new_values')
                            ->label('New Values')
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Request Info')
                    ->schema([
                        Forms\Components\TextInput::make('ip_address')
                            ->disabled(),
                        Forms\Components\TextInput::make('user_agent')
                            ->disabled()
                            ->columnSpanFull(),
                        Forms\Components\DateTimePicker::make('created_at')
                            ->disabled(),
                    ])->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('action')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'info',
                        'deleted' => 'danger',
                        'login' => 'primary',
                        'logout' => 'gray',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('auditable_type')
                    ->label('Model')
                    ->formatStateUsing(fn (string $state) => class_basename($state))
                    ->searchable(),
                Tables\Columns\TextColumn::make('auditable_id')
                    ->label('ID')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->placeholder('System'),
                Tables\Columns\TextColumn::make('ip_address')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('action')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                        'login' => 'Login',
                        'logout' => 'Logout',
                    ]),
                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
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
            'index' => Pages\ListAuditLogs::route('/'),
            'view' => Pages\ViewAuditLog::route('/{record}'),
        ];
    }
}
