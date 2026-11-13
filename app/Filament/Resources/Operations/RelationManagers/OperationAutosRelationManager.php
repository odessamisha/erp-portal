<?php

namespace App\Filament\Resources\Operations\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Filament\Schemas\Components\Tabs\Tab;

class OperationAutosRelationManager extends RelationManager
{
    protected static string $relationship = 'operation_autos';
    protected static ?string $title ='';

    public static function getTabComponent(Model $ownerRecord, string $pageClass): Tab
    {
        return Tab::make('Список авто')
            ->badge($ownerRecord->operation_autos()->count())
            ->badgeColor('info')
            ->badgeTooltip('Кол-во объектов в операции')
            ->icon('heroicon-m-document-text');
    }

    public function isReadOnly(): bool
    {
        return true;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('auto_id')
                    ->relationship('auto', 'name')
                    ->required(),
                TextInput::make('auto_name')
                    ->required(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('auto_name')
            ->columns([
                TextColumn::make('auto_name')
                    ->label('Номер авто')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('price')
                    ->label('Цена')
                    ->money('uah')
                    ->sortable()
                    ->searchable(),
              /*
                    TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                */    
            ])
            ->filters([
                //
            ])
            
            ->headerActions([
               // CreateAction::make(),
               // AssociateAction::make(),
            ])
            ->recordActions([
                // EditAction::make(),
                // DissociateAction::make(),
                // DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
                
            ]);
        
    }
}
