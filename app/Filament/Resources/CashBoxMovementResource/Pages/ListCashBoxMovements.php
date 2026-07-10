<?php

namespace App\Filament\Resources\CashBoxMovementResource\Pages;

use App\Filament\Resources\CashBoxMovementResource;
use App\Filament\Widgets\CashBoxStatsWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCashBoxMovements extends ListRecords
{
    protected static string $resource = CashBoxMovementResource::class;
    protected function getHeaderWidgets(): array
    {
        return [
            CashBoxStatsWidget::class,
        ];
    }
}
