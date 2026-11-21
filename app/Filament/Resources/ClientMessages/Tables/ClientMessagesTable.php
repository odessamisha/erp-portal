<?php

namespace App\Filament\Resources\ClientMessages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClientMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client.name')
                    ->label('Название Клиента')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Время создания')
                    ->sortable()
                    ->dateTime('d.m.Y H:i:s'),
                TextColumn::make('message')
                    ->label('Сообщение')
                    ->limit(50)
                    ->copyable()
                    ->copyMessage('Скопировано!')
                    ->copyMessageDuration(1500)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        return $state; // показує повний текст у тултіпі, якщо він обрізаний
                    })
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Статус')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->toggleable()
                    ->colors([
                        'primary' => 'sending',
                        'success' => 'delivered',
                        'danger' => 'error',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'sending' => 'Отправка',
                        'prepared' => 'Подготовлено',
                        'delivered' => 'Доставлено',
                        'read' => 'Прочитано',
                        'error' => 'Ошибка',
                        }),

                TextColumn::make('type')
                    ->label('Тип')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'incoming_message' => 'Входящее',
                        'outgoing_message' => 'Исходящее',
                        })
                    ->sortable()
                    ->searchable(),
                    
                TextColumn::make('messenger')
                    ->label('Мессенджер')
                    ->sortable()
                    ->searchable(),
                    
                    
                TextColumn::make('error_message')
                    ->label('Ошибка')
                    ->wrap()
                    ->limit(50)
                    ->sortable()
                    ->toggleable()
                    ->searchable(),

                TextColumn::make('delivered_at')
                    ->label('Дата доставки')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable()
                    ->searchable(),
            ])
            ->defaultSort('created_at', direction: 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
