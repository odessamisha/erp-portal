<?php

namespace App\Filament\Resources\OperationAutos;

use App\Filament\Resources\OperationAutos\Pages\CreateOperationAuto;
use App\Filament\Resources\OperationAutos\Pages\EditOperationAuto;
use App\Filament\Resources\OperationAutos\Pages\ListOperationAutos;
use App\Filament\Resources\OperationAutos\Schemas\OperationAutoForm;
use App\Filament\Resources\OperationAutos\Tables\OperationAutosTable;
use App\Models\OperationAuto;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OperationAutoResource extends Resource
{
    protected static ?string $model = OperationAuto::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return OperationAutoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OperationAutosTable::configure($table);
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
            'index' => ListOperationAutos::route('/'),
            'create' => CreateOperationAuto::route('/create'),
            'edit' => EditOperationAuto::route('/{record}/edit'),
        ];
    }
}
