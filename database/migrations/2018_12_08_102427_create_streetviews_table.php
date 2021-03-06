<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStreetviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('streetviews', function (Blueprint $table) {
            $table->increments('id');
            $table->float('longitude', 12, 10);
            $table->float('latitude', 12, 10);
            $table->point('location');
            $table->string('imageLink');
            $table->integer('geometry_id');
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
        Schema::dropIfExists('streetviews');
    }
}
