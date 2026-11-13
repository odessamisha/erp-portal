<?php

namespace App\Filament\Resources\TrackerSuppliers\Pages;

use App\Filament\Resources\TrackerSuppliers\TrackerSupplierResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTrackerSuppliers extends ListRecords
{
    protected static string $resource = TrackerSupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
