<?php

namespace App\Filament\Resources\OperationAutos\Pages;

use App\Filament\Resources\OperationAutos\OperationAutoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOperationAutos extends ListRecords
{
    protected static string $resource = OperationAutoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
