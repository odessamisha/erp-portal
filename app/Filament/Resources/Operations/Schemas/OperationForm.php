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
use Illuminate\Database\Eloquent\Builder;


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
                                ->columnSpan(4),
                                
                            TextInput::make('operation_flow')
                                ->label('Направление операции')
                                ->default('incoming')
                                ->readOnly(),

                            Select::make('operation_type_id')
                                ->label('Тип операции')
                                ->relationship(
                                    name: 'operation_type', 
                                    titleAttribute: 'description',
                                    modifyQueryUsing: fn (Builder $query) => $query->where('operation_flow','incoming') 
                                    )
                                ->createOptionForm([
                                    TextInput::make('operation_flow')
                                        ->label('Направление операции')
                                        ->default('incoming')
                                        ->readOnly()
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

                            TextInput::make('transaction_total')
                                ->label('Сумма операции (Полная)')
                                ->required()
                                ->numeric()
                                ->default(0)
                                ->live()
                                ->afterStateUpdated(function (Set $set, ?string $state) {
                                    $set('total', $state);
                                }),
    
                            TextInput::make('total')
                                ->label('Сумма операции (Учетная)')
                                ->required()
                                ->numeric()
                                ->default(0),
                            
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
