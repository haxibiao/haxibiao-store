<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stores', function (Blueprint $table) {
            //地址定位
            if (!Schema::hasColumn('stores', 'location_id')) {
                $table->unsignedInteger('location_id')->nullable()->index()->comment('定位地址');
            }
            //营业时间
            if (!Schema::hasColumn('stores', 'work_time')) {
                $table->string('work_time')->nullable()->comment('营业时间');
            }
            //手机号码
            if (!Schema::hasColumn('stores', 'phone_number')) {
                $table->string('phone_number')->nullable()->comment('手机号');
            }
            //微信号
            if (!Schema::hasColumn('stores', 'wechat_number')) {
                $table->string('wechat_number')->nullable()->comment('微信号');
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
        Schema::table('stores', function (Blueprint $table) {
            //
        });
    }
}
