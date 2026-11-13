<?php

namespace App\Filament\Resources\Autos\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Forms\Components\ToggleButtons;
use Filament\Support\Icons\Heroicon;

class AutoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([

                TextInput::make('name')
                    ->label('Имя объекта')
                    ->required(),

                TextInput::make('imei')
                    ->required()
                    ->label('Имей'),

                Select::make('tracker_supplier_id')
                    ->label('Модель трекера')
                    ->required()
                    ->relationship('tracker_supplier', 'name')
                    ->createOptionForm([
                        TextInput::make('name')
                        ->label('Модель трекера')
                        ->required(),
                        TextInput::make('description')
                        ->label('Полное название')
                        ->required(),
                    ])
                    ->default(1),
            
                    TextInput::make('sim_number')
                    ->label('Сим-карта (Номер)')
                    ->required(),

                TextInput::make('sim_serial_number')
                    ->label('Сим-карта (Серийник)'),
                    //->required(),    

                
                Select::make('server_id')
                    ->label('Сервер по умолчанию')
                    ->required()
                    ->relationship('server', 'name'),
                    
                Select::make('tarif_id')
                    //->columnSpan(2)
                    ->default(1)
                    ->label('Тариф рабочий')
                    ->relationship('tarif', 'name')
                    ->createOptionForm([
                        TextInput::make('name')
                        ->label('Название тарифа')
                        ->required(),
                        Select::make('tarif_type')
                        ->label('Тип тарифа')
                        ->options(['base' => 'Основной', 'sleep' => 'Для заморозки'])
                        ->required(),
                        TextInput::make('price')
                        ->label('Ставка')
                    ])
                    ->required(),

                Select::make('sleep_tarif_id')
                    //->columnSpan(2)
                    ->default(1)
                    ->label('Тариф для заморозки')
                    ->relationship('sleep_tarif', 'name')
                    ->default(3)
                    ->required(),
     
                Select::make('installer_id')
                    ->label('Установщик')
                    ->required()
                    ->relationship('installer', 'name'),

                Select::make('client_id')
                    ->columnSpan(3)
                    //->default(1)
                    ->label('Клиент')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->preload()
                    ,
                   // ->required(),

                    
                DatePicker::make('installation_date')
                    ->label('Дата установки')
                    ->default(now())
                    ->required(),    

                DatePicker::make('sleep_date')
                    ->label('Дата заморозки'),
                    
                DatePicker::make('delete_date')
                    ->label('Дата удаления'),
                
                ToggleButtons::make('status')
                    ->label('Статус объекта')
                    ->required()
                    ->default('active')
                    ->columnSpan(3)
                    ->inline()
                    ->options([
                        'active' => 'Активный',
                        'sleep' => 'Хранение',
                        'deleted' => 'Удален',
                    ])
                    ->colors([
                        'active' => 'primary',
                        'sleep' => 'warning',
                        'deleted' => 'danger',
                    ])
                    ->icons([
                        'active' => Heroicon::OutlinedCheckCircle,
                        'sleep' => Heroicon::OutlinedClock,
                        'deleted' => Heroicon::OutlinedTrash,
                    ]),

                Textarea::make('notes')
                    ->label('Комментарии')
                    ->columnSpan(3),    

            ]);
    }
}
