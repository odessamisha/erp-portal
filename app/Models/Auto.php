<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Auto extends Model
{
    use HasFactory;

    protected $fillable = ['name','imei','sim_number','sim_serial_number','tracker_supplier_id','server_id',
            'client_id','installation_date','installer_id','last_position',
            'last_sync','tarif_id','sleep_tarif_id','user_id','notes','status','delete_date','sleep_date',];
    //protected $casts = [''];        


    public function tarif(): BelongsTo
    {
        return $this->belongsTo(Tarif::class);
    }

    public function sleep_tarif(): BelongsTo
    {
        return $this->belongsTo(Tarif::class);
    }

    public function trackerSupplier(): BelongsTo
    {
        return $this->belongsTo(TrackerSupplier::class);
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function installer(): BelongsTo
    {
        return $this->belongsTo(Installer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function tracker_supplier(): BelongsTo
    {
        return $this->belongsTo(TrackerSupplier::class);
    }

 


}
