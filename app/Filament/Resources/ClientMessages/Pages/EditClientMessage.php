<?php

namespace App\Filament\Resources\ClientMessages\Pages;

use App\Filament\Resources\ClientMessages\ClientMessageResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditClientMessage extends EditRecord
{
    protected static string $resource = ClientMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
