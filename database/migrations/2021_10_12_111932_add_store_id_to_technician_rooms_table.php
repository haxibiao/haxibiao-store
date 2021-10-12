<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStoreIdToTechnicianRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('technician_rooms', function (Blueprint $table) {
            //
            if (!Schema::hasColumn('technician_rooms', 'store_id')) {
                $table->integer('store_id')->index()->comment('关联商铺id');
            }

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('technician_rooms', function (Blueprint $table) {
            //
        });
    }
}
