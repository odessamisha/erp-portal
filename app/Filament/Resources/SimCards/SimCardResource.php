<?php

namespace App\Filament\Resources\SimCards;

use App\Filament\Resources\SimCards\Pages\CreateSimCard;
use App\Filament\Resources\SimCards\Pages\EditSimCard;
use App\Filament\Resources\SimCards\Pages\ListSimCards;
use App\Filament\Resources\SimCards\Pages\ViewSimCard;
use App\Filament\Resources\SimCards\Schemas\SimCardForm;
use App\Filament\Resources\SimCards\Schemas\SimCardInfolist;
use App\Filament\Resources\SimCards\Tables\SimCardsTable;
use App\Models\SimCard;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SimCardResource extends Resource
{
    protected static ?string $model = SimCard::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string | UnitEnum | null $navigationGroup = 'Settings';

    protected static ?string $recordTitleAttribute = 'Sim Card';

    public static function form(Schema $schema): Schema
    {
        return SimCardForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SimCardInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SimCardsTable::configure($table);
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
            'index' => ListSimCards::route('/'),
            'create' => CreateSimCard::route('/create'),
            'view' => ViewSimCard::route('/{record}'),
            'edit' => EditSimCard::route('/{record}/edit'),
        ];
    }
}
