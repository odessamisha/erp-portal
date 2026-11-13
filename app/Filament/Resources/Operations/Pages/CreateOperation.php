<?php

namespace App\Filament\Resources\Operations\Pages;

use App\Filament\Resources\Operations\OperationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOperation extends CreateRecord
{
    protected static string $resource = OperationResource::class;
    protected static ?string $title = 'Новая Операция';

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
}
