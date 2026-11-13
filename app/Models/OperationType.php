<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OperationType extends Model
{
    //
    protected $fillable =['name','operation_flow', 'description'];

    use HasFactory, Notifiable;

    public function operations(): HasMany
    {
        return $this->hasMany(Operation::class);
    }

}
