<?php

namespace App\Filament\Resources\ClientMessages\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ClientMessageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('client_id')
                    ->numeric(),
                TextEntry::make('message'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('error_message')
                    ->placeholder('-'),
                TextEntry::make('delivered_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
