<?php

namespace App\Filament\Resources\Autos\Pages;

use App\Filament\Resources\Autos\AutoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Action;
use App\Models\Auto;
use App\Models\Server;
use App\Models\Client;
use Xint0\WialonPhp\Wialon;
use Xint0\WialonPhp\WialonException;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;



class ListAutos extends ListRecords
{
    protected static string $resource = AutoResource::class;

    public function getTabs(): array
    {   $counts = Auto::query()
            ->selectRaw('COUNT(CASE WHEN status = '.'"active"'.' THEN 1 END) AS is_active')
            ->selectRaw('COUNT(CASE WHEN status = '.'"deleted"'.' THEN 1 END) AS is_deleted')
            ->selectRaw('COUNT(CASE WHEN client_id IS NULL AND status = '.'"active"'.' THEN 1 END) AS without_client')
            ->selectRaw('COUNT(CASE WHEN client_id IS NOT NULL THEN 1 END) AS with_client')
            ->first();  
        return [
            

            'without_client' => Tab::make()->label('Неназначенные объекты')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->where('client_id','=', NULL)->where('status','=', 'active');
                })
                ->badge($counts->without_client)
                ->badgeColor('info'),    
                        
            'with_client' => Tab::make()->label('Назначенные объекты')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->where('client_id','!=',NULL);
                })
                ->badge($counts->with_client)
                ->badgeColor('info'),    
            
                
            'garbage' => Tab::make()->label('Корзина')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->where('status','deleted');
                })
                ->badge($counts->is_deleted)
                ->badgeColor('info')
                ->icon('heroicon-m-trash'),        
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Создать объект'),

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
            Action::make('ImportObjects')
                    ->label('Загрузка объектов из Wialon')
                    ->schema([
                        Select::make('server_id')
                            ->label('Выберите сервер')
                            ->options( function(){
                                $server_list = Server::query()->pluck('name', 'id');
                                
                                $server_list->put('all','Все сервера');
                                //dd($server_list);
                                return $server_list;
                            })
                            ->default('all')
                            ->required()
                            
                    ])
                    ->action(function (array $data){
                        if ($data['server_id'] == 'all'){
                            $servers = Server::all();
                            foreach ($servers as $server){
                                // You can specify a custom host.
                                $baseUri = $server->url;
                                $server_id = $server->id;
                                $token = $server->token;
                                $wialon = new Wialon($baseUri);
                                
                                try {
                                    $wialon->login($token);
                                    // After logging in to service a session ID is returned.
                                    $params = [
                                        'spec' => [
                                            'itemsType' => 'avl_unit',
                                            'propName' => 'sys_name',
                                            'propValueMask' => '*',
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

                                    
                                    foreach ($objects['items'] as $object){
                                        if ($object['pos'] == null)
                                            {
                                                //dd($object);
                                                $lastSync = null;
                                                $lastPosition = null;
                                            } else {
                                                $utcTime = $object['pos']['t'];
                                                $lastSync = new DateTime("@$utcTime");
                                                $lastSync->setTimezone(new DateTimeZone('Europe/Kyiv'));
                                                $lastPosition = $object['pos']['y'].' '.$object['pos']['x'];
                                            }   
                                        
                                        //проверяем есть ли уже в базе такое авто
                                        $query = Auto::where('imei', $object['uid'])
                                            ->where('server_id', $server_id)
                                            ->get();  
                                        //если авто в базе нет, то мы его добавляем
                                        if (count($query) == 0){
                                            $record = array(
                                                'user_id' => Auth::user()->id,
                                                'name'  => $object['nm'],
                                                'imei' => $object['uid'],
                                                'sim_number' => $object['ph'],
                                                'tracker_supplier_id' => 1,
                                                'server_id' => $server_id,
                                                'last_position' => $lastPosition,
                                                'last_sync' => $lastSync,
                                                'tarif_id' => 1,
                                                'sleep_tarif_id' => 3,
                                            );
                                            $cur_auto = Auto::create($record);
                                        } 
                                        //если авто в базе есть, то мы его обновяем
                                        else {
                                            $cur_auto = $query[0];
                                            $cur_auto['name'] = $object['nm'];
                                            $cur_auto['sim_number'] = $object['ph'];
                                            $cur_auto['last_position'] = $lastPosition;
                                            $cur_auto['last_sync'] = $lastSync;
                                            $cur_auto['server_id'] = $server_id;
                                            $cur_auto['status'] = 'active';
                                            $cur_auto->save();
                                        }

                                        
                                    }
                                    $wialon->logout();
                                } catch (WialonException $exception) {
                                    // WialonException is thrown when request fails or service returns error response.
                                }
                            }
                        } else {
                            $query = Server::get()->where('id', $data['server_id']);
                            $server = $query->first();
                            $baseUri = $server->url;
                            $server_id = $server->id;
                            $token = $server->token;
                            $wialon = new Wialon($baseUri);
                            try {
                                    $wialon->login($token);
                                    //dd($wialon);
                                    // After logging in to service a session ID is returned.
                                    $params = [
                                        'spec' => [
                                            'itemsType' => 'avl_unit',
                                            'propName' => 'sys_name',
                                            'propValueMask' => '*',
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

                                    
                                    foreach ($objects['items'] as $object){
                                        if ($object['pos'] == null)
                                            {
                                                //dd($object);
                                                $lastSync = null;
                                                $lastPosition = null;
                                            } else {
                                                $utcTime = $object['pos']['t'];
                                                $lastSync = new DateTime("@$utcTime");
                                                $lastSync->setTimezone(new DateTimeZone('Europe/Kyiv'));
                                                $lastPosition = $object['pos']['y'].' '.$object['pos']['x'];
                                            }   
                                        
                                        //проверяем есть ли уже в базе такое авто
                                        $query = Auto::where('imei', $object['uid'])
                                            ->where('server_id', $server_id)
                                            ->get();  
                                        //если авто в базе нет, то мы его добавляем
                                        if (count($query) == 0){
                                            $record = array(
                                                'user_id' => Auth::user()->id,
                                                'name'  => $object['nm'],
                                                'imei' => $object['uid'],
                                                'sim_number' => $object['ph'],
                                                'tracker_supplier_id' => 1,
                                                'server_id' => $server_id,
                                                'last_position' => $lastPosition,
                                                'last_sync' => $lastSync,
                                                'tarif_id' => 1,
                                                'sleep_tarif_id' => 3,
                                            );
                                            $cur_auto = Auto::create($record);
                                        } 
                                        //если авто в базе есть, то мы его обновяем
                                        else {
                                            $cur_auto = $query[0];
                                            $cur_auto['name'] = $object['nm'];
                                            $cur_auto['sim_number'] = $object['ph'];
                                            $cur_auto['last_position'] = $lastPosition;
                                            $cur_auto['last_sync'] = $lastSync;
                                            $cur_auto['server_id'] = $server_id;
                                            $cur_auto['status'] = 'active';
                                            $cur_auto->save();
                                        } 
                                    }
                                    $wialon->logout();
                                } catch (WialonException $exception) {
                                    // WialonException is thrown when request fails or service returns error response.
                                }
                        }
                    })
                //aaa

        ];
    }
}
