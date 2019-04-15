<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InitialViewParameters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('initialViewParameters', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('street_id');
            $table->float('yaw', 22, 20);
            $table->float('pitch', 22, 20);
            $table->float('fov', 22, 20);
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
