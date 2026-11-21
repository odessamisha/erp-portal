<?php

namespace App\Filament\Resources\ClientMessages;

use App\Filament\Resources\ClientMessages\Pages\CreateClientMessage;
use App\Filament\Resources\ClientMessages\Pages\EditClientMessage;
use App\Filament\Resources\ClientMessages\Pages\ListClientMessages;
use App\Filament\Resources\ClientMessages\Pages\ViewClientMessage;
use App\Filament\Resources\ClientMessages\Schemas\ClientMessageForm;
use App\Filament\Resources\ClientMessages\Schemas\ClientMessageInfolist;
use App\Filament\Resources\ClientMessages\Tables\ClientMessagesTable;
use App\Models\ClientMessage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ClientMessageResource extends Resource
{
    protected static ?string $model = ClientMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'client.name';

    protected static ?string $pluralModelLabel = 'Обмен сообщениями';

    public static function form(Schema $schema): Schema
    {
        return ClientMessageForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ClientMessageInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClientMessagesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClientMessages::route('/'),
            'create' => CreateClientMessage::route('/create'),
            'view' => ViewClientMessage::route('/{record}'),
            'edit' => EditClientMessage::route('/{record}/edit'),
        ];
    }
}
