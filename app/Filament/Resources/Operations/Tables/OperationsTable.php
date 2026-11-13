<?php

namespace App\Filament\Resources\Operations\Tables;

use App\Models\Auto;
//use App\Models\Client;
use App\Models\Operation;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Enums\RecordActionsPosition;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\Layout\Split;
//use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Range;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Mail;

use function PHPUnit\Framework\assertNotTrue;
use Illuminate\Database\Eloquent\Builder;

//use Illuminate\Pagination\CursorPaginator;
use Filament\Tables\Filters\Filter;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Illuminate\Database\Eloquent\Collection;

class OperationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client.name')
                    ->label('Название клиента')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('operation_flow')
                    ->label('Операция')
                    ->badge()
                    ->sortable(),
                TextColumn::make('operation_period')
                    ->label('Период')
                    ->state(fn ($record) => "{$record->period_month} {$record->period_year}")
                    ->searchable(['period_month', 'period_year']),

                TextColumn::make('operation_autos_count')
                    ->numeric()
                    ->label('Кол-во машин')
                    ->counts(['operation_autos' => fn (Builder $query) => $query,])
                    ->toggleable(isToggledHiddenByDefault: true),
                        

                TextColumn::make('total')
                    ->label('Сумма операции')
                    ->money('UAH')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('note')
                    ->label('Содержание')
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        return $state; // показує повний текст у тултіпі, якщо він обрізаний
                    })    ,

                    
                TextColumn::make('operation_ts')
                    ->label('Дата операции')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable()
                    ,
            ])
            ->filters([
                //
                DateRangeFilter::make('operation_ts')
                    ->label('Дата операции'),
                
            ],)             
            ->selectable()
            ->defaultSort('operation_ts','desc')
            ->recordActions([
                
                EditAction::make()
                    ->iconButton()
                    ->hidden(fn (Operation $record) : bool => $record->operation_flow === 'payroll' || $record->active == false),        
                
                Action::make('toGarbage1')
                        ->label('Перенести в корзину')
                        ->iconButton()
                        ->color('primary')
                        ->icon('heroicon-o-inbox-arrow-down')
                        ->hidden(fn (Operation $record) : bool => $record->active == false || $record->client_id <> NULL)
                        ->requiresConfirmation()
                        ->action(function ($record){
                                $record->active = false;
                                $record->save();
                }),
                
                Action::make('fromGarbage')
                        ->label('Перенести из корзины')
                        ->iconButton()
                        ->color('primary')
                        ->icon('heroicon-o-document-arrow-up')
                        ->hidden(fn (Operation $record) : bool => $record->active == true)        
                        ->requiresConfirmation()
                        ->action(function ($record){
                                $record->active = true;
                                $record->save();
                })
                ,
                /*
                DeleteAction::make()
                    ->label('Удалить из БД')
                    ->iconButton()
                    ->hidden(fn (Operation $record) : bool => $record->operation_flow === 'payroll' || $record->active == false)        
            */        
                ],
               
            //position: RecordActionsPosition::BeforeCells
            )
            ->toolbarActions([
                BulkActionGroup::make([
                    //DeleteBulkAction::make(),
                    BulkAction::make('toGarbage')
                        ->label('Перенести в корзину')
                        ->color('primary')
                        ->icon('heroicon-o-trash')
                        ->requiresConfirmation()
                        ->action(function ($records){
                            foreach($records as $record){    
                                $record->active = false;
                                $record->save();
                            }
                        })
                    
                ]),

                Action::make('AddClient')
                    ->label('Добавить клиента')
                    ->url('/admin/clients/create')
            ]);
    }
}
