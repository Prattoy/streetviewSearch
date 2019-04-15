<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class defaultLinkHotspots extends Model
{
    protected $table='defaultlinkHotspots';
    protected $fillable = [
        'geometry_id',
        'yaw',
        'pitch',
        'rotation',
    ];
}
