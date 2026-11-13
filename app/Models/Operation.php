<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Operation extends Model
{
    protected $fillable = ['client_id','user_id','operation_flow','operation_type_id','operation_ts','operation_code',
                            'operation_ref','period_year','period_month','note','attachment','total','transaction_total', 
                            'linked_operation_id','status','active'];
    protected $casts = [
        'active' => 'boolean'
    ];


    public function client(): BelongsTo{
        return $this->belongsTo(Client::class);
    }

    public function operation_type(): BelongsTo{
        return $this->belongsTo(OperationType::class);
    }

    public function operation_autos(): hasMany
    {
        return $this->hasMany(OperationAuto::class);
    }
}
