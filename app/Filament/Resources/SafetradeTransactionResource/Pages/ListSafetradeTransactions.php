<?php

namespace App\Filament\Resources\SafetradeTransactionResource\Pages;

use App\Filament\Resources\SafetradeTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSafetradeTransactions extends ListRecords
{
    protected static string $resource = SafetradeTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
