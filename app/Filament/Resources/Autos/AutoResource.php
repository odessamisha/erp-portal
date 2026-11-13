<?php

namespace App\Filament\Resources\Autos;

use App\Filament\Resources\Autos\Pages\CreateAuto;
use App\Filament\Resources\Autos\Pages\EditAuto;
use App\Filament\Resources\Autos\Pages\ListAutos;
use App\Filament\Resources\Autos\Schemas\AutoForm;
use App\Filament\Resources\Autos\Tables\AutosTable;
use App\Models\Auto;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AutoResource extends Resource
{
    protected static ?string $model = Auto::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;
    protected static ?string $modelLabel = 'Объект';
    protected static ?string $pluralModelLabel = 'Список Объектов';
    protected static string | UnitEnum | null $navigationGroup = 'Справочники';



    protected static ?string $recordTitleAttribute = 'Auto';

    public static function form(Schema $schema): Schema
    {
        return AutoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AutosTable::configure($table);
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
            'index' => ListAutos::route('/'),
            'create' => CreateAuto::route('/create'),
            'edit' => EditAuto::route('/{record}/edit'),
        ];
    }
}
