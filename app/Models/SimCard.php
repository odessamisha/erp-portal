<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['serial_number', 'phone_number', 'sim_supplier_id', 'is_active'])]
class SimCard extends Model
{
    //
    public function simSupplier(): BelongsTo
    {
        return $this->belongsTo(SimSupplier::class);
    }
}
