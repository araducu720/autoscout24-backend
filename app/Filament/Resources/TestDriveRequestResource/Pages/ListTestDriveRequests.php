<?php

namespace App\Filament\Resources\TestDriveRequestResource\Pages;

use App\Filament\Resources\TestDriveRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTestDriveRequests extends ListRecords
{
    protected static string $resource = TestDriveRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
