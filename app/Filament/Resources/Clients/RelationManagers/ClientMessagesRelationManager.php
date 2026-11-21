<?php

namespace App\Filament\Resources\Clients\RelationManagers;

use App\Models\ClientMessage;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Tabs\Tab;

class ClientMessagesRelationManager extends RelationManager
{
    protected static string $relationship = 'client_messages';
    protected static ?string $title ='Сообщения клиента';
 
    public static function getTabComponent(Model $ownerRecord, string $pageClass): Tab
    {
        return Tab::make('Сообщения клиента')
            ->badge($ownerRecord->client_messages()->count())
            ->badgeColor('info')
            ->badgeTooltip('Кол-во сообщений клиента')
            ->icon('heroicon-m-chat-bubble-left-right');
    }
    
    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                RichEditor::make('message')
                    ->label('Сообщение')
                    ->required()
                    ->maxLength(65535),
                
            ]);
    }
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Время создания')
                    ->sortable()
                    ->dateTime('d.m.Y H:i:s'),
                TextColumn::make('message')
                    ->label('Сообщение')
                    ->limit(50)
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
            ->headerActions([
                CreateAction::make(),
            ]);
    }
    
}
