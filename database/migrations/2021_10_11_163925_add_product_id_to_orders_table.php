<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductIdToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'product_id')) {
                $table->unsignedInteger('product_id')->index()->nullable()->comment('关联项目');
            }
            if (!Schema::hasColumn('orders', 'technician_room_id')) {
                $table->unsignedInteger('technician_room_id')->index()->nullable()->comment('关联房间');
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
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
}
