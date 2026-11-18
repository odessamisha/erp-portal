<?php

namespace App\Filament\Resources\ClientMessages\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ClientMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('client_id')
                    ->relationship('client', 'name')
                    ->required()
                    ->label('Клиент'),
                    
                TextInput::make('message')
                    ->required(),
                Select::make('status')
                    ->options(['sending' => 'Sending', 'delivered' => 'Delivered', 'error' => 'Error'])
                    ->default('sending')
                    ->required(),
                TextInput::make('error_message'),
                DateTimePicker::make('delivered_at'),
            ]);
    }
}
