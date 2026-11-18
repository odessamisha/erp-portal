<?php

namespace App\Filament\Resources\ClientMessages\Pages;

use App\Filament\Resources\ClientMessages\ClientMessageResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewClientMessage extends ViewRecord
{
    protected static string $resource = ClientMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
