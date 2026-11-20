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
    public function created(Operation $operation): void
    {
        //
       // dd($operation);
        if ($operation->client_id <> NULL)
            {
                $oprTotal = ($operation->total ?? 0);    
                $this->adjustBalance($operation, $oprTotal);

                if($operation->operation_flow == 'incoming' && $oprTotal > 0){
                    $this->sendMessage($operation->client, "Ваша оплата на сумму {$oprTotal}грн. від {$operation->operation_ts} була успішно прийнята.\n 
                    Ваш поточний баланс: {$operation->client->balance}грн."); 
                }

                if($operation->operation_flow == 'payroll' && $oprTotal < 0){
                    $this->sendMessage($operation->client, "Вам нарахована сумму ".abs($oprTotal)."грн. від {$operation->operation_ts} за обладнання або роботи.\n 
                    Ваш поточний баланс: {$operation->client->balance}грн."); 
                }
            }
        
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
            $this->sendMessage($operation->client, "Ваша оплата на сумму {$operation->total}грн. від {$operation->operation_ts} була успішно прийнята.\n 
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
            $this->sendMessage($operation->client, "Ваш баланс пополнен на сумму {$diff}грн. від {$operation->operation_ts}.\n 
            Ваш поточний баланс: {$operation->client->balance}грн."); 
        }
        else{
            if ($originalTotal == 0){
                $this->sendMessage($operation->client, "Нарахована абонплата за {$operation->period_month}-{$operation->period_year} на суму ".abs($diff)."грн. від {$operation->operation_ts}.\n 
                Ваш поточний баланс: {$operation->client->balance}грн.");
            }
            else {
                $this->sendMessage($operation->client, "Перерахована абонплата за {$operation->period_month}-{$operation->period_year} на суму ".abs($diff)."грн. від {$operation->operation_ts}.\n 
                Ваш поточний баланс: {$operation->client->balance}грн.");
            }
        }
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
