<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Deleted extends Model
{
    protected $table='deleted';
    protected $fillable = [
	    'imageLink',
        'geometry_id',

    ];
}
