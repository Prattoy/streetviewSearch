<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Streetview extends Model
{
    //
    protected $table='streetviews';
    protected $fillable = [
        'longtitude',
        'latitude',
	    'imageLink',
	    'url',
        'geometry_id',

    ];
}
