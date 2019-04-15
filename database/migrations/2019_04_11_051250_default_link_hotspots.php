<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DefaultLinkHotspots extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('defaultlinkHotspots', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('geometry_id');
            $table->float('yaw', 22, 20);
            $table->float('pitch', 22, 20);
            $table->float('rotation');
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
