<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['operation_id','auto_id','auto_name','price'])]
class OperationAuto extends Model
{
    //
    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }

    public function auto(): BelongsTo
    {
        return $this->belongsTo(Auto::class);
    }

}
