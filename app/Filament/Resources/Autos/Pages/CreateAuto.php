<?php

namespace App\Filament\Resources\Autos\Pages;

use App\Filament\Resources\Autos\AutoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAuto extends CreateRecord
{
    protected static string $resource = AutoResource::class;

    protected function getRedirectUrl(): string
    {
        //return $this->getResource()::getUrl('index');
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
}
