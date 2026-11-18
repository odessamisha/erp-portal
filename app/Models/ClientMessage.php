<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientMessage extends Model
{
    //
    protected $fillable = ['client_id','messenger','message_id','message','status','type','error_message','delivered_at'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    
}
