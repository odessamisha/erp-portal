<?php

namespace App\Filament\Resources\SimCards\Pages;

use App\Filament\Resources\SimCards\SimCardResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSimCard extends EditRecord
{
    protected static string $resource = SimCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
