<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Tarif extends Model
{
    use HasFactory;

    protected $fillable = ['name','price','is_active'];
    protected $casts = ['is_active' => 'boolean'];
}
