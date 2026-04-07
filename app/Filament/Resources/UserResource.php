<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Account Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->maxLength(255)
                            ->helperText(fn (string $operation) => $operation === 'edit' ? 'Leave blank to keep current password' : ''),
                    ])->columns(2),

                Forms\Components\Section::make('Profile')
                    ->schema([
                        Forms\Components\FileUpload::make('avatar')
                            ->image()
                            ->avatar()
                            ->directory('avatars')
                            ->maxSize(2048),
                        Forms\Components\Select::make('locale')
                            ->options([
                                'en' => 'English',
                                'de' => 'Deutsch',
                                'fr' => 'Français',
                                'es' => 'Español',
                                'it' => 'Italiano',
                                'nl' => 'Nederlands',
                                'ro' => 'Română',
                            ])
                            ->default('de'),
                        Forms\Components\Select::make('currency')
                            ->options([
                                'EUR' => 'EUR (€)',
                                'CHF' => 'CHF (Fr.)',
                                'GBP' => 'GBP (£)',
                            ])
                            ->default('EUR'),
                        Forms\Components\TextInput::make('country')
                            ->maxLength(100),
                    ])->columns(2),

                Forms\Components\Section::make('Bank Details')
                    ->schema([
                        Forms\Components\TextInput::make('bank_name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('iban')
                            ->label('IBAN')
                            ->maxLength(34),
                        Forms\Components\TextInput::make('bic')
                            ->label('BIC/SWIFT')
                            ->maxLength(11),
                        Forms\Components\TextInput::make('account_holder')
                            ->maxLength(255),
                        Forms\Components\Toggle::make('bank_details_verified')
                            ->label('Bank Details Verified'),
                        Forms\Components\DateTimePicker::make('bank_details_verified_at')
                            ->label('Verified At')
                            ->disabled(),
                    ])->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('Roles & Permissions')
                    ->schema([
                        Forms\Components\Toggle::make('is_admin')
                            ->label('Administrator')
                            ->helperText('Grant admin panel access')
                            ->disabled(fn (?\App\Models\User $record) => $record?->id === auth()->id()),
                        Forms\Components\DateTimePicker::make('email_verified_at')
                            ->label('Email Verified At'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name)),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_admin')
                    ->boolean()
                    ->label('Admin'),
                Tables\Columns\IconColumn::make('bank_details_verified')
                    ->boolean()
                    ->label('Bank Verified')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Not verified')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('locale')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_admin')
                    ->label('Admins')
                    ->placeholder('All Users')
                    ->trueLabel('Admins Only')
                    ->falseLabel('Regular Users'),
                Tables\Filters\TernaryFilter::make('email_verified_at')
                    ->label('Email Verified')
                    ->nullable()
                    ->placeholder('All')
                    ->trueLabel('Verified')
                    ->falseLabel('Not Verified'),
                Tables\Filters\TernaryFilter::make('bank_details_verified')
                    ->label('Bank Verified'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn ($record) => $record->id === auth()->id()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('verify_email')
                        ->label('Verify Email')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each(fn ($r) => $r->update(['email_verified_at' => $r->email_verified_at ?? now()])))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('verify_bank')
                        ->label('Verify Bank Details')
                        ->icon('heroicon-o-building-library')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each(fn ($r) => $r->update([
                            'bank_details_verified' => true,
                            'bank_details_verified_at' => $r->bank_details_verified_at ?? now(),
                        ])))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\VehiclesRelationManager::class,
            RelationManagers\ReviewsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
