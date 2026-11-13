<?php

namespace App\Filament\Resources\Operations;

use App\Filament\Resources\Operations\Pages\CreateOperation;
use App\Filament\Resources\Operations\Pages\EditOperation;
use App\Filament\Resources\Operations\Pages\ListOperations;
use App\Filament\Resources\Operations\Pages\ViewOperation;
use App\Filament\Resources\Operations\Schemas\OperationForm;
use App\Filament\Resources\Operations\Tables\OperationsTable;
use App\Models\Operation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class OperationResource extends Resource
{
    protected static ?string $model = Operation::class;

    //protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    //protected static string | UnitEnum | null $navigationGroup = 'Основные Операции';
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $recordTitleAttribute = 'Operation';

    protected static ?string $modelLabel = 'Операция';
    protected static ?string $pluralModelLabel = 'Список операций';

    public static function form(Schema $schema): Schema
    {
        return OperationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OperationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
            RelationManagers\OperationAutosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOperations::route('/'),
            'create' => CreateOperation::route('/create'),
            'view' => ViewOperation::route('/{record}'),
            'edit' => EditOperation::route('/{record}/edit'),
        ];
    }
}
