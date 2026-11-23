<?php

namespace App\Filament\Resources\Operations\Schemas;

use App\Models\Client;
use App\Models\User;
use App\Models\OperationType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\TextSize;
use Filament\Support\Enums\FontWeight;


class OperationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')
                ->columnSpanFull()
                ->tabs([
                    Tab::make('Основная информация')
                        ->columns(4)
                        ->schema([
                            Select::make('client_id')
                                ->label('Название клиента')
                                ->placeholder('Выбрать клиента')
                                ->options(Client::query()->pluck('name', 'id'))
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live()
                                ->columnSpan(4),
                                
                            Select::make('operation_flow')
                                ->label('Направление операции')
                                ->default('incoming')
                                ->options([
                                    'incoming' => 'Оплата',
                                    'payroll' => 'Начисление/Продажа',
                                ])
                                ->live(),

                            Select::make('operation_type_id')
                                ->label('Тип операции')
                                ->relationship(
                                    name: 'operation_type', 
                                    titleAttribute: 'description',
                                    modifyQueryUsing: function (Builder $query, Get $get) {
                                        if ($get('operation_flow') === 'incoming') {
                                            return $query->where('operation_flow','incoming');
                                        } elseif ($get('operation_flow') === 'payroll') {
                                            return $query->where('operation_flow','payroll');
                                        }
                                    } 
                                    
                                     
                                    )
                                ->createOptionForm([
                                    TextInput::make('operation_flow')
                                        ->label('Направление операции')
                                        ->default('incoming')
                                        //->readOnly()
                                        ->required(),
                                    TextInput::make('name')
                                        ->label('Название операции')
                                        ->required(),
                                    TextInput::make('description')
                                        ->label('Описание операции')
                                        ->required(),
                                ])
                                    
                                ->required()
                                ->columnSpan(2),
                            Select::make('status')
                                ->label('Статус операции')
                                ->options(['pending' => 'В процесса', 'approved' => 'Подтверждена'])
                                ->default('approved')
                                //->readOnly()
                                ->required(),

                            
                            Select::make('period_year')
                                ->label('Год операции')
                                ->required()
                                ->options(
                                    array_combine(
                                        range(date('Y'), date('Y') + 10),
                                        range(date('Y'), date('Y') + 10)
                                    )
                                ),
                            
                            Select::make('period_month')
                                ->label('Месяц операции')
                                ->required()
                                ->options([
                                    'Jan' => 'Январь',
                                    'Feb' => 'Февраль',
                                    'Mar' => 'Март',
                                    'Apr' => 'Апрель',
                                    'May' => 'Май',
                                    'Jun' => 'Июнь',
                                    'Jul' => 'Июль',
                                    'Aug' => 'Август',
                                    'Sep' => 'Сентябрь',
                                    'Oct' => 'Октябрь',
                                    'Nov' => 'Ноябрь',
                                    'Dec' => 'Декабрь',
                                ]),

                            DateTimePicker::make('operation_ts')
                                ->label('Дата операции')
                                ->default(now())
                                //->format('d-m-Y h:m:s')
                                ->required(),
                            
                            TextInput::make('operation_code')
                                ->label('Код операции')
                                ->default(fn() => str()->random())
                                ->readOnly()
                                ->required(),

                            TextInput::make('total')
                                ->label('Сумма операции')
                                ->minValue(1)
                                ->required()
                                ->numeric()
                                ->default(0),
                            
                                
                            TextEntry::make('balance')
                                ->label('Баланс клиента')
                                ->default(function (Set $set, Get $get){
                                    $clientId = $get('client_id');
                                    if ($clientId) {
                                        $client = Client::find($clientId);
                                        return $client ? $client->balance : 0;
                                    }
                                    return 0;
                                })
                                ->color(fn(int $state): string => $state < 0? 'danger' : 'success')
                                ->money('UAH')
                                ->size(TextSize::Large)
                                ->weight(FontWeight::ExtraBold)
                                ->alignEnd()
                                ->columnSpan(3)
                                ,
                                
                            
                            RichEditor::make('note')
                                ->label('Содержание операции')
                                ->columnSpanFull(),
                            
                            
                            
                    ]),         
                    
                    Tab::make('Доп. информация')
                        ->schema([
                            FileUpload::make('attachment')
                                ->label('Приложение')
                                ->columnSpanFull(),
    
                            Select::make('user_id')
                                ->label('Имя пользователя')
                                ->options(User::query()->pluck('name', 'id'))
                                ->default(1)
                                ->required(),
    
    
                        ])

                   ])
                
            ]);
    }
}
