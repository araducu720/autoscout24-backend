<?php

namespace App\Filament\Resources\SafetradeTransactionResource\Pages;

use App\Filament\Resources\SafetradeTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSafetradeTransaction extends EditRecord
{
    protected static string $resource = SafetradeTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
