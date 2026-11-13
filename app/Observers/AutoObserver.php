<?php

namespace App\Observers;

use App\Models\Auto;
use App\Models\Tarif;
use App\Models\Operation;
use App\Models\OperationAuto;
use Filament\Notifications\Notification;


class AutoObserver
{
    /**
     * Handle the Auto "created" event.
     */
    public function created(Auto $auto): void
    {
        //
        $cur_day = date('d');
        //dd($cur_day);

    }

    /**
     * Handle the Auto "updated" event.
     */
    public function updated(Auto $auto): void
    {
           
        $prevStatus = $auto->getOriginal('status');
        $newStatus = $auto->status;
        $payroll_type = $auto->client->payroll_type ?? 'none';
        
        
        $cur_day = date('d');
        $cur_month = date('M');
        $cur_year = date('Y');
        if ($cur_day < 15 and $payroll_type <> 'prepay'){
            //dd($cur_year);


        }
        //замена тарифа
        
        if ($auto->wasChanged('tarif_id') and $payroll_type == 'prepay' and $newStatus == 'active'){
            $oldTarifId = $auto->getOriginal('tarif_id');
            $oldTarif = Tarif::query()->where('id',$oldTarifId)->get()[0]->price ?? 0;
            $newTarif = $auto->tarif->price ?? 0;
            $diffTarif = $newTarif - $oldTarif;
            
            if ($diffTarif>0){
                $this->adjustOperation($auto,$diffTarif); 
            }
        }
        if ($prevStatus == 'deleted' and $newStatus == 'active'){
            return;
        }

        if ($auto->wasChanged('client_id') and $auto->getOriginal('client_id') == NULL and $prevStatus == 'active'){
            $this->adjustOperation($auto,$auto->tarif->price); 
        }

        if ($auto->wasChanged('client_id') and $payroll_type == 'prepay' and $prevStatus == 'deleted'){
            $this->adjustOperation($auto,$auto->tarif->price);   
            $auto->update(['status' => 'active']);
            return;
        }


        if (! $auto->wasChanged('status')){
            return;
        }   

        if ($newStatus == 'active' and $prevStatus == 'sleep' and $payroll_type == 'prepay'){
            $diff = $auto->tarif->price - $auto->sleep_tarif->price;
            $this->adjustOperation($auto,$diff);   
        }
        
       
        if ($newStatus == 'deleted' and $payroll_type == 'prepay' and !$auto->wasChanged('client_id')){
            $auto->update(['client_id' => NULL]);
        }
    }
    protected function adjustOperation (Auto $auto, int $sum): void {
        
        $client_id = $auto->client_id;
        $cur_day = date('d');
        $cur_month = date('M');
        $cur_year = date('Y');

        $operation = Operation::firstOrNew([
            'client_id' => $client_id,
            'operation_flow' => 'payroll',
            'period_month' => $cur_month,
            'period_year' => $cur_year,
            'operation_type_id' => 1,
            'status' => 'approved'

        ]);
        $operation->operation_code = $operation->operation_code ?? str()->random();
        $operation->operation_ts = $operation->operation_ts ?? now();
        
        $operation->total = $operation->total - $sum;
        $operation->transaction_total = $operation->transaction_total - $sum;
        $operation->save();

        $opr_id = $operation->id;
        $operationAuto = OperationAuto::firstOrNew([
                'operation_id' => $opr_id,
                'auto_id' => $auto->id
            ]);
        $operationAuto->auto_name = $auto->name;    
        $operationAuto->price = $auto->tarif->price;
        $operationAuto->save();    
    }
    /**
     * Handle the Auto "deleted" event.
     */
    public function deleted(Auto $auto): void
    {
        //
    }

    /**
     * Handle the Auto "restored" event.
     */
    public function restored(Auto $auto): void
    {
        //
    }

    /**
     * Handle the Auto "force deleted" event.
     */
    public function forceDeleted(Auto $auto): void
    {
        //
    }
}
