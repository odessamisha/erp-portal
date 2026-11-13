<?php

namespace App\Filament\Resources\TrackerSuppliers\Pages;

use App\Filament\Resources\TrackerSuppliers\TrackerSupplierResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTrackerSupplier extends EditRecord
{
    protected static string $resource = TrackerSupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
