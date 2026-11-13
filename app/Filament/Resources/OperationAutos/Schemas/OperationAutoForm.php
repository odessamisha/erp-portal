<?php

namespace App\Filament\Resources\OperationAutos\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OperationAutoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('operation_id')
                    ->required()
                    ->numeric(),
                TextInput::make('auto_id')
                    ->required()
                    ->numeric(),
                TextInput::make('auto_name')
                    ->required(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
            ]);
    }
}
