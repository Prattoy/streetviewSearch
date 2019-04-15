<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StreetviewNew extends Model
{
    protected $table='streetviews_new';
    protected $fillable = [
        'longtitude',
        'latitude',
	    'point_id',
	    'faceSize',
        'geometry_id',

    ];
}
