<?php

namespace App\Filament\Resources\Operations\Pages;

use App\Filament\Resources\Operations\OperationResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;

use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Carbon\Carbon;

use App\Models\Client;
use App\Models\Operation;
use App\Models\OperationAuto;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Sashalenz\Privat24BusinessApi\Privat24BusinessApi;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Collection;

class ListOperations extends ListRecords
{
    protected static string $resource = OperationResource::class;

    public function getTabs(): array
    {
        $counts = Operation::query()
            ->selectRaw('COUNT(CASE WHEN active = '.'1'.' THEN 1 END) AS is_active')
            ->selectRaw('COUNT(CASE WHEN active = '.'0'.' THEN 1 END) AS is_deleted')
            ->selectRaw('COUNT(CASE WHEN client_id IS NOT NULL AND operation_flow = '.'"payroll"'.' AND active = '.'1'.' THEN 1 END) AS payroll')
            ->selectRaw('COUNT(CASE WHEN client_id IS NOT NULL AND operation_flow = '.'"incoming"'.' AND active = '.'1'.' THEN 1 END) AS incoming')
            ->selectRaw('COUNT(CASE WHEN client_id IS NULL AND operation_flow = '.'"incoming"'.'AND status = '.'"pending"'.' AND active = '.'1'.' THEN 1 END) AS pending')
            ->selectRaw('COUNT(CASE WHEN client_id IS NOT NULL THEN 1 END) AS with_client')
            ->first();  

        return [
            'all' => Tab::make()->label('Активные операции')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->where('active',true);
                })
                ->icon('heroicon-m-lifebuoy')
                ->badge($counts->is_active)
                ->badgeColor('info'),
            
            'payroll' => Tab::make()->label('Начисления')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->where('operation_flow','payroll')->where('active',true);
                })
                ->icon('heroicon-m-circle-stack')
                ->badge($counts->payroll)
                ->badgeColor('info'),
            
                'incoming_bank' => Tab::make()->label('Поступления на счет')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->where('operation_flow','incoming')->where('active',true);
                })
                ->icon('heroicon-m-banknotes')
                ->badge($counts->incoming)
                ->badgeColor('info'),
                
            'dont_approved' => Tab::make()->label('Неопознанные платежи')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->where('status','pending')->where('operation_flow','incoming')->where('active',true);
                })
                ->icon('heroicon-m-magnifying-glass')
                ->badge($counts->pending)
                ->badgeColor('danger'),

            'garbage' => Tab::make()->label('Корзина')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->where('active',false)->where('operation_flow','incoming');
                })
                ->icon('heroicon-m-trash')
                ->badge($counts->is_deleted)
                ->badgeColor('info')
                ,        
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Создать операцию вручную'),
            Action::make('PayrollProcess')
                    ->label('Запустить начисление абонплаты')
                    ->icon('heroicon-m-calculator')
                    ->schema([

                        Select::make('period_month')
                            ->label('Выберите месяц начисления')
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
                            ])
                            ->default(date('M'))
                            ->required(),
                        Select::make('period_year')
                            ->label('Выберите год начисления')
                            ->options(
                                array_combine(
                                    range(date('Y'), date('Y') + 10),
                                    range(date('Y'), date('Y') + 10)
                                )
                            )
                            ->default(date('Y'))
                            ->required(),

                    ])
                    ->action(function (array $data) {
                        //отбираем всех активных клиентов
                        $clients = Client::query()->where('clients.is_active', true)->get();
                        foreach ($clients as $client) {
                            //код_клиента
                            $client_id = $client->id;
                            //тип начисления
                            $client_payroll_type = $client->payroll_type;
                            
                            //проверяем есть ли уже в базе такое начисление
                            $query = Operation::where('operation_flow', 'payroll')
                                ->where('period_year',$data['period_year'])
                                ->where('period_month',$data['period_month'])
                                ->where('client_id', $client_id)
                                ->get();

                            if (count($query) == 0) {
                                //общая сумма операции
                                $total = 0;
                                
                                //отбираем все авто клиента
                                $autos = $client->autos;
                                if (count($autos) <> 0) {
                                    $record = array(
                                    'client_id' => $client_id,
                                    'user_id' => Auth::user()->id,
                                    'operation_flow' => 'payroll',
                                    'operation_type_id' => 1,
                                    'operation_code' => str()->random(),
                                    'operation_ts' => now(),
                                    'period_month' => $data['period_month'],
                                    'period_year' => $data['period_year'],
                                    'status' => 'approved'
                                    );

                                    $cur_operation = Operation::create($record);

                                    //Проходимся по всем авто клиента
                                    
                                    foreach ($autos as $auto) {
                                        switch ($auto->status) {
                                            case 'active': 
                                                $cur_price = $auto->tarif->price;
                                                break;
                                            case 'sleep': 
                                                $cur_price = $auto->sleep_tarif->price;
                                                break;    
                                            case 'deleted':
                                                $cur_price = 0;
                                                break;    
                                        }

                                        $total += $cur_price;
                                        
                                        $obj = array(
                                            'operation_id' => $cur_operation->id,
                                            'auto_id' => $auto->id,
                                            'auto_name' => $auto->name,
                                            'price' => $cur_price,
                                        );
                                        
                                        OperationAuto::create($obj);
                                        /* проверка даты отключения.
                                            После отключения авто становится свободным
                                        */
                                        if ($auto->delete_date !== null) {
                                            $delete_month = date('M', strtotime($auto->delete_date));
                                            $delete_year = date('Y', strtotime($auto->delete_date));
                                            if ($delete_month == $data['period_month'] && $delete_year == $data['period_year']) {
                                                $auto['client_id'] = null;
                                                $auto['status'] = 'deleted';
                                                $auto['delete_date'] = null;
                                                $auto['sleep_date'] = null;
                                                $auto->save();
                                            }
                                        }
                                
                                        /* проверка даты заморозки.
                                        */
                                        if ($auto->sleep_date !== null) {
                                            $sleep_month = date('M', strtotime($auto->sleep_date));
                                            $sleep_year = date('Y', strtotime($auto->sleep_date));
                                            if ($sleep_month == $data['period_month'] && $sleep_year == $data['period_year']) {
                                                $auto['status'] = 'sleep';
                                                $auto->save();
                                            }
                                        }    
                                    }    

                                    $cur_operation->total = -1*$total;
                                    $cur_operation->transaction_total = -1*$total;
                                    
                                    $cur_operation->save();
                                   // $client->balance -= $total;
                                    $client->save();   
                                }
                            }
                        }        
                    }),
            Action::make('IncomingProcess')
                    ->label('Импорт платежей')
                    ->icon('heroicon-m-calculator')
                    ->schema([
                        Section::make()
                            ->columns(2)
                            ->schema([
                                DatePicker::make('start_date')
                                    ->label('Начальная дата')
                                    ->inlineLabel()
                                    ->native(false)
                                    ->time(false)
                                    ->maxDate(now()->subDays(1))
                                    ->default(now()->startOfMonth()->format('d-m-Y'))
                                    ->displayFormat('d-m-Y')
                                    ->placeholder(now()->startOfMonth()->format('d-m-Y'))
                                    ->closeOnDateSelection()
                                ,
                                DatePicker::make('end_date') 
                                    ->label('Конечная дата')
                                    ->inlineLabel()
                                    ->native(false)
                                    ->time(false)
                                    ->displayFormat('d-m-Y')
                                    ->maxDate(now())
                                    ->default(now()->format('d-m-Y'))
                                    ->placeholder(now()->format('d-m-Y'))
                                    ->closeOnDateSelection()
                                    ,
                            ])
                    ])
                    ->action(function (array $data) {
                        $token ='e85a8456-eee6-4b74-9c42-909fbab5a7ac+Q6kbs4GHyxgZrjg+/WQQjkA4yo8KmIvJBoYGrtA23Eqtnb+7GjLEUi4u8HfuYpdAnK9Tqr8oixowhDMB++dAJefMSSYQuw9AnGC0V6MxBzSvmXNPdgQ9GqiYk87xEOOYuZShfkb3E58CEd6HxRb6K7rGR0+twWsm0LioNLDw9bQDfzbYlx16iITyY8jWsdbe3SEg2+VETbceeT2swZXOol/WmwEVkM32s3OXWoNuTxd9iBME4QhXc/s6kygxlwVFHzIwH0=';
                        $followId = '';
                        $startDate = Carbon::parse($data['start_date']);
                        $endDate = Carbon::parse($data['end_date']);
                        $acc = 'UA383052990000026003044922252';
                        $exist_next_page = true;
                        
                        while ($exist_next_page) {
                            $response = Privat24BusinessApi::statements()
                                ->token($token)
                                ->transactions(
                                    startDate: $startDate,
                                    endDate: $endDate,
                                    acc: $acc,
                                    limit: 10,
                                    followId: $followId
                            );
                        
                            //проверяем есть ли след. страница
                            $exist_next_page = $response->exist_next_page;
                            if ($exist_next_page){
                                //передаем сслыку на след. страницу
                                $followId = $response->next_page_id;
                            }                
                            //#1 перебираем все транзакции из банка
                            //dd($response->transactions);
                            if (count($response->transactions) > 0){
                                foreach ($response->transactions as $tx) {
                                    //проверяем тип операции
                                    if ($tx->TRANTYPE == 'C'){
                                        //проверяем есть ли такая операция в базе
                                        $query = Operation::where('operation_code', $tx->NUM_DOC)->get();
                                        if (count($query) == 0){
                                            $record = array(
                                                'client_id' => null,
                                                'user_id' => Auth::user()->id,
                                                'operation_flow' => 'incoming',
                                                'operation_type_id' => 2,
                                                'operation_code' => $tx->NUM_DOC,
                                                'operation_ref' => $tx->REF,
                                                'operation_ts' => $tx->DATE_TIME_DAT_OD_TIM_P->toDateTimeString(),
                                                'period_month' => date('M'),
                                                'period_year' => date('Y'),
                                                'total' => $tx->SUM,
                                                'transaction_total' => $tx->SUM,
                                                'status' => 'pending',
                                                'active' => true,
                                                'note' => $tx->AUT_CNTR_NAM.' => '.$tx->OSND
                                            );
                                            //сохраняем операцию в базе
                                            $cur_operation = Operation::create($record);
                                        }
                                        
                                    }
                                    
                                }
                            }
                            //#1
                        }
                            
                            
                    }),

                
        ];
    }
}
