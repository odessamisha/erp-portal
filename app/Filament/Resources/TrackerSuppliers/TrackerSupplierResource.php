<?php

namespace App\Filament\Resources\TrackerSuppliers;

use App\Filament\Resources\TrackerSuppliers\Pages\CreateTrackerSupplier;
use App\Filament\Resources\TrackerSuppliers\Pages\EditTrackerSupplier;
use App\Filament\Resources\TrackerSuppliers\Pages\ListTrackerSuppliers;
use App\Filament\Resources\TrackerSuppliers\Schemas\TrackerSupplierForm;
use App\Filament\Resources\TrackerSuppliers\Tables\TrackerSuppliersTable;
use App\Models\TrackerSupplier;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TrackerSupplierResource extends Resource
{
    protected static ?string $model = TrackerSupplier::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string | UnitEnum | null $navigationGroup = 'Settings';

    public static function form(Schema $schema): Schema
    {
        return TrackerSupplierForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrackerSuppliersTable::configure($table);
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
            'index' => ListTrackerSuppliers::route('/'),
            'create' => CreateTrackerSupplier::route('/create'),
            'edit' => EditTrackerSupplier::route('/{record}/edit'),
        ];
    }
}
