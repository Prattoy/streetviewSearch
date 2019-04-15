<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class linkHotSpots extends Model
{
    protected $table='linkHotspots';
    protected $fillable = [
        'street_id',
        'yaw',
        'pitch',
        'rotation',
        'target'
    ];
}
