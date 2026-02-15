<?php

namespace App\Filament\Resources\StockReportResource\Pages;

use App\Filament\Resources\StockReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStockReports extends ListRecords
{
    protected static string $resource = StockReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
