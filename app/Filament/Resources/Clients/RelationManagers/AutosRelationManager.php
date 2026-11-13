<?php

namespace App\Filament\Resources\Clients\RelationManagers;

use App\Models\Auto;
use Filament\Actions\Action;
use Filament\Actions\AssociateAction;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Filament\Actions\ActionGroup;
use Filament\Tables\Enums\RecordActionsPosition;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Support\Icons\Heroicon;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Builder;

class AutosRelationManager extends RelationManager
{
    protected static string $relationship = 'autos';
    protected static ?string $title ='';
   // protected static ?string $modelLabel = 'name';
    


    public static function getTabComponent(Model $ownerRecord, string $pageClass): Tab
    {
        return Tab::make('Список авто')
            ->badge($ownerRecord->autos()->count())
            ->badgeColor('info')
            ->badgeTooltip('Кол-во объектов клиента')
            ->icon('heroicon-m-truck');
    }

    public function form(Schema $schema): Schema
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
                    ])
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                            if ($state == 'active'){
                                $set('sleep_date',NULL);
                                $set('delete_date',NULL);
                            }
                            if ($state == 'sleep'){
                                $set('sleep_date',now());
                                $set('delete_date',NULL);
                            }

                            if ($state == 'deleted'){
                                $set('sleep_date',NULL);
                                $set('delete_date',now());
                            }

                    }),
                Textarea::make('notes')
                    ->label('Комментарии')
                    ->columnSpan(3),        

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Название')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('tarif.name')
                    ->label('Тариф(рабочий)')
                    ->toggleable(),        
                
                TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'sleep' => 'warning',
                        'deleted' => 'danger',
                        })
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'active' => 'Активный',
                            'sleep' => 'Хранение',
                            'deleted' => 'Удален',
                        }),

                TextColumn::make('installation_date')
                    ->label('Дата установки')
                    ->date('d.m.Y')
                    ->sortable()
                    ->toggleable(),
                    
                TextColumn::make('sleep_date')
                    ->label('Дата заморозки')
                    ->date('d.m.Y')
                    ->sortable()
                    ->toggleable(),
        
                TextColumn::make('imei')
                    ->label('Имей')
                    ->copyable()
                    ->copyMessage('Скопировано!')
                    ->copyMessageDuration(1500)
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('sim_number')
                    ->label('Сим-карта')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),    
                
                TextColumn::make('trackersupplier.name')
                    ->label('Трекер')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('server.name')
                    ->label('Сервер')
                    ->sortable()
                    ->searchable(),
                
                
                TextColumn::make('last_sync')
                    ->label('Дата Синхронизации')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),    
            
                TextColumn::make('sleep_tarif.name')
                    ->label('Тариф(заморозка)')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
            
                              
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                      ->modalHeading('Редактировать запись'),
                    //DetachAction::make(),
                    Action::make('toGarbage')
                        ->label('Перенести в корзину')
                        ->icon('heroicon-o-trash'),
                    
                    
                    DeleteAction::make()
                        ->label('Удалить из БД')    
                ])
            ], position: RecordActionsPosition::BeforeColumns)
            
            ->headerActions([
                 
            ])

            ->toolbarActions([
                CreateAction::make()
                    ->label('Создать новое авто'),
                
                AssociateAction::make('assoc1')
                    ->label('Перенести авто от другого клиента')
                    ->color('primary')
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(fn (Builder $query) => $query->whereNotNull('client_id')->where('status','<>','deleted')),
                    
                AssociateAction::make('assoc2')
                    ->label('Перенести авто из корзины')
                    ->color('primary')
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(fn (Builder $query) => $query->where('status','deleted')),    
            ]);
    }
}
