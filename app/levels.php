<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class levels extends Model
{
    protected $table='levels';
    protected $fillable = [
        'street_id',
        'tileSize',
        'size',
        'fallbackOnly',
    ];
}
