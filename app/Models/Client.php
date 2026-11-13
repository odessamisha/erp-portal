<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Client extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = ['name','username','client_type','payroll_type','server_id','provider_id','manager_id','city_id','address',
            'contact_name','contact_phone','contract_number','contract_date','exchange_id','exchange_data',
            'buhgalter_name','buhgalter_phone','buhgalter_email','bank_account','delivery_address','balance','user_id',
            'is_active','is_locked','other'];

    protected $casts = [
        'is_active' => 'boolean',
        'is_locked' => 'boolean'
        ];

    public function autos(): HasMany
    {
        return $this->hasMany(Auto::class);
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Manager::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function exchange(): BelongsTo
    {
        return $this->belongsTo(Exchange::class);
    }

    public function operations(): hasMany
    {
        return $this->hasMany(Operation::class);
    }


}
