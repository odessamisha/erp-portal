<?php
//branch1
namespace App\Filament\Resources\Clients\Tables;

use App\Models\Client;
use App\Models\Auto;
use App\Models\Server;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Select;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Xint0\WialonPhp\Wialon;
use Xint0\WialonPhp\WialonException;
use App\Filament\Resources\AutoResource;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('is_active')
                    ->label('#')
                    ->boolean(),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable('ASC')
                    ->label('Название клиента'),


                TextColumn::make('provider.name')
                    ->label('Поставщик услуг')
                    ->sortable(),

                TextColumn::make('autos_count')
                    ->numeric()
                    ->label('Объекты')
                    ->counts(['autos' => fn (Builder $query) => $query,]),

                TextColumn::make('balance')
                    ->label('Баланс')
                    ->numeric()
                    ->weight(FontWeight::Bold)
                    ->color(fn(int $state): string => $state < 0? 'danger' : 'success')
                    ->sortable(),

                ToggleColumn::make('is_locked')
                    ->label('Статус')
                    ->offColor('success')
                    ->onColor('danger')
                    ->onIcon(Heroicon::Bolt)
                    ->offIcon(Heroicon::User),
            ])
            ->defaultSort('balance','asc')
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()->label('Редактировать')
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
                CreateAction::make()->label('Добавить клиента'),
                            
                Action::make('SearchObjects')
                    ->label('Поиск объектов по коду клиента')
                    ->schema([
                        Select::make('client_id')
                            ->label('Выберите имя клиента')
                            ->options( function(){
                                    $client_list = Client::query()->pluck('name', 'id');            
                                    return $client_list;
                                })    
                            ->required()
                            ->searchable()
                    ])
                    ->action(function (array $data){
                    $query = Client::get()->where('id', $data['client_id']);
                    $selected_client = $query->first();
                    $username = $selected_client->username;
                    $server_id = $selected_client->server_id;
                    $query = Server::get()->where('id', $server_id);
                    $server = $query->first();
                    $baseUri = $server->url;
                    $token = $server->token;
                    $wialon = new Wialon($baseUri);
                            
                    try {
                        $wialon->login($token);
                        // After logging in to service a session ID is returned.
                        //поиск ID пользователя
                        // for branch 2
                        $svc = 'core/search_items';
                        $params = [
                            'spec' => [
                                'itemsType' => 'user',
                                'propName' => 'sys_name',
                                'propValueMask' => $username,
                                'sortType' => '',
                                'propType' => '',
                                'or_logic' => false,
                            ],
                            'force' => 1,
                            'flags' => 0x501,
                            'from' => 0,
                            'to' => 0,
                        ];
                        $objects = $wialon->unitByName($svc,$params);
                        //dd($objects);
                        if (count($objects['items']) <> 0)
                            {
                                $user_id = $objects['items'][0]['id'];
                                //выбираем все доступные объекты
                                $svc = 'user/get_items_access';
                                $params = [
                                    'userId' => $user_id,
                                    'directAccess' => false,
                                    'itemSuperclass' => 'avl_unit',
                                    'flags' => '0x2'
                                ];
                                $objects = $wialon->unitByName($svc,$params);
                                $object_list = array_keys($objects);
                                foreach ($object_list as $item){
                                    $params = [
                                        'spec' => [
                                            'itemsType' => 'avl_unit',
                                            'propName' => 'sys_id',
                                            'propValueMask' => $item,
                                            'sortType' => '',
                                            'propType' => '',
                                            'or_logic' => false,
                                        ],
                                        'force' => 1,
                                        'flags' => 0x501,
                                        'from' => 0,
                                        'to' => 0,
                                    ];
                                    $svc = 'core/search_items';
                                    $objects = $wialon->unitByName($svc,$params);
                                    $cur_auto_imei = $objects['items'][0]['uid'];
                                    //dd($cur_auto_id);
                                    //проверяем есть ли уже в базе такое авто
                                    $query = Auto::where('imei', $cur_auto_imei)
                                        ->where('server_id', $server_id)
                                        ->get();  
                                    $cur_auto = $query[0];
                                    $cur_auto['client_id'] = $data['client_id'];
                                    $cur_auto->save();    
                                }
                            }
                        
                        $wialon->logout();

                    } catch (WialonException $exception) {
                        // WialonException is thrown when request fails or service returns error response.
                    }
                }),  

                Action::make('AddAuto')
                    ->label('Добавить авто')
                    ->url('/admin/autos/create')
            ]);
    }
}
