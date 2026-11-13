<?php

namespace App\Filament\Resources\OperationAutos\Pages;

use App\Filament\Resources\OperationAutos\OperationAutoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOperationAuto extends EditRecord
{
    protected static string $resource = OperationAutoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
