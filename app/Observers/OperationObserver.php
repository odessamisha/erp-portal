<?php

namespace App\Observers;

use App\Models\Operation;
use App\Models\Client;
use App\Models\ClientMessage;

class OperationObserver
{
    /**
     * Handle the Operation "created" event.
     */

    protected string $payStr = '

Дані для оплати:
1)Через Приват 24
Згідно інструкцій
https://pb.ua/short?5YYLS
2)Через інші банки 
Найменування отримувача:  ФОП Станков Артем Михайлович
Код одержувача: 3821110911
Рахунок отримувача у форматі  IBAN: UA38 305299 00000 26003044922252 Станков А. М. ФОП

Призначення платежу:  Оплата за послуги GPS моніторингу транспорта 

Особистий рахунок - Ваш логин (или Номер телефона)

!!!ПРОХАННЯ ПІСЛЯ ОПЛАТИ – ВІДПИШИТЬСЯ!!!!';

    public function created(Operation $operation): void
    {

        if ($operation->client_id <> NULL && (! $operation->wasChanged('total')))
            {
                $oprTotal = ($operation->total ?? 0);    
                $this->adjustBalance($operation, $oprTotal);
                
                if ($operation->operation_flow == 'payroll' && $operation->operation_type_id  == 8){
                    $full_message = "Вам нарахована сумма ".abs($operation->total)."грн. від {$operation->operation_ts} за виконані роботи. 
Ваш поточний баланс: {$operation->client->balance}грн."; 
                }

                if ($operation->operation_flow == 'payroll' && $operation->operation_type_id  == 7){
                    $full_message = "Вам нарахована сумма ".abs($operation->total)."грн. від {$operation->operation_ts} за обладнання. 
Ваш поточний баланс: {$operation->client->balance}грн."; 
                }


                if ($operation->operation_flow == 'incoming' && $oprTotal > 0){
                    $full_message = "Ваша оплата на сумму {$oprTotal}грн. від {$operation->operation_ts} була успішно прийнята.
                    Ваш поточний баланс: {$operation->client->balance}грн."; 
                }

                
            }
        //отправка сообщения
        $this->sendMessage($operation->client,$full_message);
        
    }

    /**
     * Handle the Operation "updated" event.
     */
    public function updated(Operation $operation): void
    {
        //прием платежа, если статус изменился с pending на approved
        if ($operation->wasChanged('status') && $operation->getOriginal('status') === 'pending')
        {
            $cur_balance = $operation->client->balance + $operation->total ?? 0;
            $this->sendMessage($operation->client, "Ваша оплата на сумму {$operation->total}грн. від {$operation->operation_ts} була успішно прийнята.
            Ваш поточний баланс: {$cur_balance}грн."); 
 
            $this->adjustBalance($operation, $operation->total);
        }
        //отмена платежа, если статус изменился с approved на pending
        if ($operation->wasChanged('status') && $operation->getOriginal('status') === 'approved' && $operation->status === 'pending')
        {
            $this->adjustBalance($operation, -1*$operation->total);
        }

        if ($operation->wasChanged('client_id'))
            {
               // $this->adjustBalance($operation, $operation->total);
            }

        if (! $operation->wasChanged('total')){
            return;
        }
        $originalTotal = $operation->getOriginal('total');
        $diff = $operation->total - $originalTotal;
        $this->adjustBalance($operation, $diff);
        if ($diff > 0){
            $full_message = "Ваш баланс пополнен на сумму {$diff}грн. від {$operation->operation_ts}.
            Ваш поточний баланс: {$operation->client->balance}грн.";     
        }
        else{
            if ($originalTotal == 0){
                    $full_message = "Нарахована абонплата за {$operation->period_month}-{$operation->period_year} на суму ".abs($diff)."грн. від {$operation->operation_ts}. 
Ваш поточний баланс: {$operation->client->balance}грн.";
                    
             }
            else {
                    $full_message = "Перерахована абонплата за {$operation->period_month}-{$operation->period_year} на суму ".abs($diff)."грн. від {$operation->operation_ts}. 
Ваш поточний баланс: {$operation->client->balance}грн.";
            }
        }
        if ($operation->client->balance < 0){
            $full_message = $full_message.$this->payStr;
        }
        $this->sendMessage($operation->client,$full_message);
    }

    /**
     * Handle the Operation "deleted" event.
     */
    public function deleted(Operation $operation): void
    {
        //
        $this->adjustBalance($operation, -1 * $operation->total);
    }

    protected function adjustBalance(Operation $operation, int $opr_total): void
    {
        if ($opr_total == 0){
            return;
        }
        $client_id = $operation->client_id;
        if ($client_id <> NULL){
            $client = Client::firstOrNew([
            'id' => $client_id,
            ]);
            $client->balance =($client->balance ?? 0) + $opr_total;  
            $client->save();
        }
          
    } 

    protected function sendMessage(Client $client, string $message): void
    {
        $clientMessage = new ClientMessage();
        $clientMessage->client_id = $client->id;
        $clientMessage->message_id = str()->random();
        $clientMessage->message = $message ? $message : 'No message';
        $clientMessage->status = 'prepared';
        $clientMessage->type = 'outgoing_message';
        $clientMessage->messenger = $client->exchange->name ?? 'viber';
        //dd($clientMessage);
        $clientMessage->save();
          
    }
    /**
     * Handle the Operation "restored" event.
     */
    public function restored(Operation $operation): void
    {
        //
    }

    /**
     * Handle the Operation "force deleted" event.
     */
    public function forceDeleted(Operation $operation): void
    {
        //
    }
}
