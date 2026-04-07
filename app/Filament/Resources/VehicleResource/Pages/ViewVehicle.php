<?php

namespace App\Filament\Resources\VehicleResource\Pages;

use App\Filament\Resources\VehicleResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewVehicle extends ViewRecord
{
    protected static string $resource = VehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('toggle_featured')
                ->label(fn ($record) => $record->is_featured ? 'Remove Featured' : 'Mark Featured')
                ->icon(fn ($record) => $record->is_featured ? 'heroicon-o-star' : 'heroicon-s-star')
                ->color(fn ($record) => $record->is_featured ? 'warning' : 'success')
                ->requiresConfirmation()
                ->action(fn ($record) => $record->update(['is_featured' => !$record->is_featured])),
        ];
    }
}
