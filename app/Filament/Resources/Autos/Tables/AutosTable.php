<?php

namespace App\Filament\Resources\Autos\Tables;
use App\Models\Auto;
use App\Models\Client;
use App\Models\Server;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Support\Icons\Heroicon;


use Carbon\Carbon;
use DateTime;
use DateTimeZone;

class AutosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Название')
                    ->sortable()
                    ->searchable(),
                
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
                    ->toggleable(),
                
                    TextColumn::make('trackersupplier.name')
                    ->label('Трекер')
                    ->sortable(),
                
                    TextColumn::make('server.name')
                    ->label('Сервер')
                    ->sortable()
                    ->searchable(),
                
                    TextColumn::make('client.name')
                    ->label('Клиент')
                    ->sortable()
                    ->searchable(),
                
                    TextColumn::make('installation_date')
                    ->label('Дата установки')
                    ->date('d.m.Y')
                    ->sortable()
                    ->toggleable(),
                
                    TextColumn::make('last_sync')
                    ->label('Дата Синхронизации')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable()->toggleable(),
                
                    TextColumn::make('tarif.name')
                    ->label('Тариф(рабочий)')
                    ->toggleable(isToggledHiddenByDefault: true),    
                
                    TextColumn::make('sleep_tarif.name')
                    ->label('Тариф(заморозка)')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('delete_date')
                    ->label('Дата удаления')
                    ->date('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),  
                
                    TextColumn::make('sleep_date')
                    ->label('Дата заморозки')
                    ->date('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),  

                
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
                
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),

                    Action::make('clientLink')
                        ->label('Назначить клиенту')
                        ->icon('heroicon-o-arrow-right-end-on-rectangle'),
                    
                    Action::make('toGarbage')
                        ->label('Перевести на удаление')
                        ->icon('heroicon-o-trash'),
                    
                    Action::make('toSleep')
                        ->label('Перевести на хранение')
                        ->icon('heroicon-o-archive-box-x-mark'),
                    
                    DeleteAction::make()
                        ->label('Удалить из БД')    
                ])
            ], position: RecordActionsPosition::BeforeColumns)
            
            ->toolbarActions([
                BulkActionGroup::make([
                    //DeleteBulkAction::make(),
                    BulkAction::make('BulkClientLink')
                        ->label('Назначить к клиенту')
                        ->icon('heroicon-m-arrow-right-end-on-rectangle')
                        ->schema([
                            Select::make('client_id')
                                ->label('Выберите название клиента')
                                ->options( fn() => Client::query()->pluck('name', 'id')),

                        ])
                        ->action(function (Collection $selectedRecords, array $data){
                            $selectedRecords->each(fn (Model $selectedRecord) => $selectedRecord->update(['client_id' => $data['client_id']]));
                        })
                ]),
                
            ]);
    }
}
