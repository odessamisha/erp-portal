<?php

namespace App\Filament\Resources\SimCards\Schemas;

use App\Models\SimSupplier;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SimCardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('serial_number')
                    ->required(),
                TextInput::make('phone_number')
                    ->tel()
                    ->required(),
                Select::make('sim_supplier_id')
                    ->required()
                    ->label('Оператор')
                    ->options(SimSupplier::query()->pluck('name', 'id')),
                Toggle::make('is_active')
                    ->required()
                    ->label('Активная')
                    ->default(true),
            ]);
    }
}
