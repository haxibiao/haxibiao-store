<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTechnicianRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('technician_rooms', function (Blueprint $table) {
            $table->id();
            $table->integer('uids')->index()->comment('客户们的id');
            $table->integer('tids')->index()->comment('技师们的id');
            $table->integer('status')->default(0)->comment('0空闲中|1忙碌中');
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
        Schema::dropIfExists('technician_rooms');
    }
}
