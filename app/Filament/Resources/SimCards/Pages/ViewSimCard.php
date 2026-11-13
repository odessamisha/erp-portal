<?php

namespace App\Filament\Resources\SimCards\Pages;

use App\Filament\Resources\SimCards\SimCardResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSimCard extends ViewRecord
{
    protected static string $resource = SimCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
