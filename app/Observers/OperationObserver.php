<?php

namespace App\Observers;

use App\Models\Operation;
use App\Models\Client   ;

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
            }
        
    }

    /**
     * Handle the Operation "updated" event.
     */
    public function updated(Operation $operation): void
    {
        if ($operation->wasChanged('client_id'))
            {
                $this->adjustBalance($operation, $operation->total);
            }

        if (! $operation->wasChanged('total')){
            return;
        }
        $originalTotal = $operation->getOriginal('total');
        $diff = $operation->total - $originalTotal;
        $this->adjustBalance($operation, $diff);
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
