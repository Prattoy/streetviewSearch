<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class initialViewParams extends Model
{
    protected $table='initialViewParameters';
    protected $fillable = [
        'street_id',
        'yaw',
        'pitch',
        'fov',
    ];
}
