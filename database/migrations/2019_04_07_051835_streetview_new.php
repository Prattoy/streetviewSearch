<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StreetviewNew extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('streetviews_new', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('geometry_id');
            $table->float('longitude', 12, 10);
            $table->float('latitude', 12, 10);
            $table->point('location');
            $table->string('point_id');
            $table->integer('faceSize');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
