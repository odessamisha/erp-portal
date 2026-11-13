<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListClients extends ListRecords
{
    protected static string $resource = ClientResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make()->label('Все клиенты'),
            'private' => Tab::make()->label('Физ. лица')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->where('client_type','private');
                }),
            'business' => Tab::make()->label('Юр. лица')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->where('client_type','business');
                }),
            'dolgi' => Tab::make()->label('Должники')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->where('balance','<',0);
                }),
            'block' => Tab::make()->label('Заблокированные')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->where('is_locked',true);
                }),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
