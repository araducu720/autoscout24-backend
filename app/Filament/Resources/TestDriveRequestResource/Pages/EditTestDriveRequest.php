<?php

namespace App\Filament\Resources\TestDriveRequestResource\Pages;

use App\Filament\Resources\TestDriveRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTestDriveRequest extends EditRecord
{
    protected static string $resource = TestDriveRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
