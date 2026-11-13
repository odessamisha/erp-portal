<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ToggleColumn;
use Tiptap\Nodes\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Fieldset;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')
                ->columnSpanFull()
                ->tabs([
                    Tab::make('Основная информация')
                        ->columns(3)
                        ->schema([
                            Section::make()
                                ->columnSpan(2)
                                ->columns(3)
                                ->schema([
                                    TextInput::make('name')
                                        ->columnSpan(2)
                                        ->label('Название клиента')
                                        ->required(),

                                    Select::make('client_type')
                                        ->default('private')
                                        ->label('Тип клиента')
                                        ->options(['private' => 'Физ. лицо', 'business' => 'Юр. лицо'])
                                        ->required()
                                        ->live()
                                        ,
                                    Select::make('payroll_type')
                                        ->label('Тип начисления')
                                        ->default('prepay')
                                        ->options(['prepay' => 'Предоплата (PREPAY)', 'postpay' => 'По факту (POSTPAY)'])
                                        ->required()
                                        ,
                                    Select::make('provider_id')
                                        ->columnSpan(2)
                                        ->default(1)
                                        ->label('Поставщик услуг')
                                        ->relationship('provider', 'name')
                                        ->required(),

                                    Fieldset::make()
                                        ->columns(3)
                                        ->schema([
                                            TextInput::make('contract_number')
                                                ->columnSpan(2)
                                                ->label('Номер договора'),

                                            DatePicker::make('contract_date')
                                                ->label('Дата договора')
                                                ->default(now()),
                                        ])
                                        ->hidden(fn (Get $get): bool => $get('client_type') == 'private')
                                        ->columnSpan(3),
                                    
                                    Select::make('manager_id')
                                        ->default(1)
                                        ->columnSpan(2)
                                        ->label('Менеджер')
                                        ->relationship('manager', 'name')
                                        ->required(),

                                    Select::make('city_id')
                                        ->columnSpan(1)
                                        ->label('Город')
                                        ->relationship('city', 'name')
                                        ->createOptionForm([
                                            TextInput::make('name')
                                                ->label('Введите название города')
                                                ->required(),    
                                        ])
                                        ->required(),
                                ]),
                            Section::make()
                                ->schema([
                                    TextInput::make('balance')
                                        ->label('Текущий баланс')
                                        ->default(0)
                                        ->prefix('UAH')
                                        //->inlineLabel()
                                        //->readOnly(fn (string $operation): bool => $operation === 'edit')
                                        ,
                                        
                                    ToggleButtons::make('is_locked')
                                        ->label('Статус блокировки')
                                        ->boolean()
                                        ->inline()
                                        ->default(false)
                                        ->options([
                                            true => 'Заблокирован',
                                            false => 'Активный'
                                        ])
                                        ->colors([
                                            true => 'danger',
                                            false => 'primary'
                                        ])
                                        ->icons([
                                            true => Heroicon::OutlinedBoltSlash,
                                            false => Heroicon::OutlinedBolt,
                                            
                                        ])
                                        ,
                                    TextInput::make('user_id')    
                                        ->default(Auth::user()->id)
                                        ->hidden(fn (string $operation): bool => $operation === 'edit'),
                                ]),

                            Section::make()
                                ->columnSpan(3)
                                ->schema([
                                    RichEditor::make('other')
                                        ->label('Дополнительная информация')
                                ])
                        ]),
                    Tab::make('Контакты')
                        ->columns(2)
                        ->schema([
                            Section::make()
                                ->schema([

                                    TextInput::make('address')
                                        ->hidden(fn (Get $get): bool => $get('client_type') == 'private')
                                        ->label('Юридический адрес:'),

                                    TextInput::make('delivery_address')
                                        ->label('Почтовый адрес (Укр Почта/Новая Почта):'),

                                    TextInput::make('contact_name')
                                        ->label('Контактное лицо:'),

                                    TextInput::make('contact_phone')
                                        ->label('Контактный номер телефона:')
                                        ->tel()
                                        ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                                        ->trim()
                                ]),
                            Section::make()
                                ->hidden(fn (Get $get): bool => $get('client_type') == 'private')
                                ->schema([
                                    TextInput::make('buhgalter_name')
                                        ->label('Имя бухгалтера:'),
                                    TextInput::make('buhgalter_phone')
                                        ->label('Номер телефона бухгалтера:')
                                        ->tel(),
                                    TextInput::make('buhgalter_email')
                                        ->label('Email бухгалтера:')
                                        ->email(),

                                    TextInput::make('bank_account')
                                        ->label('Расчетный счет:')
                                        ->email()
                                ])


                        ]),
                    Tab::make('Документооборот')
                        ->columns(2)
                        ->schema([
                            Select::make('exchange_id')
                                ->label('Тип документооборота')
                                ->relationship('exchange', 'name')
                                ->default(1),
                            TextInput::make('exchange_data')
                                ->label('Данные для обмена')
                        ]),
                    Tab::make('Система мониторинга')
                    ->columns(2)
                    ->schema([
                        TextInput::make('username')
                            ->label('Имя пользователя (Логин):')
                            ->placeholder('username')
                            ->trim(),

                        Select::make('server_id')
                            ->label('Адрес сервера:')
                            ->required()
                            ->relationship('server', 'name'),
                        TextInput::make('access_token')
                            ->columnSpan(2)
                            ->label('Токен доступа:')
                    ])
                ])

            ]);
    }
}
