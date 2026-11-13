<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'description'])]
class SimSupplier extends Model
{
    use HasFactory, Notifiable;

    public function simcards(): HasMany
    {
        return $this->hasMany(SimCard::class, 'simcard_id');
    }
}
