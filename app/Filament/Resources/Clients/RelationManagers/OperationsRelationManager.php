<?php

namespace App\Filament\Resources\Clients\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\OperationAuto;

class OperationsRelationManager extends RelationManager
{
    protected static string $relationship = 'operations';
    protected static ?string $title ='Просмотр операций';
    public static function getTabComponent(Model $ownerRecord, string $pageClass): Tab
    {
        return Tab::make('Список операций')
            ->badge($ownerRecord->operations()->count())
            ->badgeColor('info')
            ->badgeTooltip('Кол-во операций клиента')
            ->icon('heroicon-m-calculator');
    }

    

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('operation_type')
                    ->label('Тип операции')
                    ->options(['payroll' => 'Payroll', 'incoming' => 'Incoming'])
                    ->required(),
                //TextInput::make(''),    
                
                TextInput::make('total')
                    ->label('Сумма операции')
                    ->required()
                    ->numeric()
                    ->default(0),

                Select::make('period_year')
                    ->label('Год начисления')
                    ->required()
                    ->options(
                        array_combine(
                            range(date('Y'), date('Y') + 10),
                            range(date('Y'), date('Y') + 10)
                        )
                    ),
                    
                Select::make('period_month')
                    ->label('Месяц операции')
                    ->options([
                        'Jan' => 'Январь',
                        'Feb' => 'Февраль',
                        'Mar' => 'Март',
                        'Apr' => 'Апрель',
                        'May' => 'Май',
                        'Jun' => 'Июнь',
                        'Jul' => 'Июль',
                        'Aug' => 'Август',
                        'Dep' => 'Сентябрь',
                        'Oct' => 'Октябрь',
                        'Nov' => 'Ноябрь',
                        'Dec' => 'Декабрь',
                    ])
                    ->required(),
                
                    Textarea::make('note')
                    ->label('Содержание операции')
                    ->columnSpanFull(),
                
                    TextInput::make('attachment')
                    ->label('Приложение'),
                
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('operation_flow')
                    ->label('Тип операции')
                    ->searchable()
                    ->badge(),
                TextColumn::make('period_year')
                    ->label('Год')
                    ->searchable()
                    ->sortable(),
                
                    TextColumn::make('period_month')
                    ->label('Месяц')
                    ->searchable()
                    ->sortable(),
                
                    TextColumn::make('operation_autos_count')
                    ->numeric()
                    ->label('Кол-во машин')
                    ->counts(['operation_autos' => fn (Builder $query) => $query,])
                    ->action(
                        Action::make('view_details')
                            ->label('Детали начисления')
                            ->modal()
                            ->modalSubmitAction(false)
                            ->modalCancelAction(fn() => null) 
                            ->modalContent(fn($record) => view('operationViewAutos', ['record' => $record]))

                    ),
                
                    TextColumn::make('total')
                    ->label('Сумма операции')
                    ->money('uah')
                    ->searchable()
                    ->sortable(),
                    
                    TextColumn::make('operation_ts')
                    ->label('Дата операции')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable(),
            ])       
            ->filters([
                //
            ])
            ->headerActions([
               // CreateAction::make(),
              //  AssociateAction::make(),
              //Action::make('custom_action')->label('Custom action'),
            ])
            ->recordActions([
                //EditAction::make(),
                //ViewAction::make(),
               // DeleteAction::make(),
                Action::make('OperationDetail')
                    ->label('Детали операции')
                    ->modal()
                    ->modalSubmitAction(false)
                    ->modalCancelAction(fn() => null)
                    ->modalContent(fn($record) => view('operationViewAutos', ['record' => $record]))
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    } 
}
