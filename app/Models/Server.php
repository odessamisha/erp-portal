<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'server_type', 'url', 'token'])]

class Server extends Model
{
    use HasFactory, Notifiable;

    public function autos(): HasMany
    {
        return $this->hasMany(Auto::class);
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }
}
