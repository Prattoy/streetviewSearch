<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GeoRoad extends Model
{
    protected $table='geo_road';
    protected $fillable = [
        'geometry_id',
        'road_name',

    ];
}
