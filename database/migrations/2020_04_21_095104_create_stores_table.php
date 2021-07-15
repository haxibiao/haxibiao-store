<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id');
            $table->String('name')->comment("名称");
            $table->String('description')->comment("分类描述");
            $table->unsignedInteger('location_id')->nullable()->index()->comment('定位地址');
            $table->string('work_time')->nullable()->comment('营业时间');
            $table->string('phone_number')->nullable()->comment('手机号');
            $table->string('wechat_number')->nullable()->comment('微信号');
            $table->Integer('status')->comment("1：上架，-1下架");
            $table->json('data')->nullable()->comment('审核信息');
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
        Schema::dropIfExists('stores');
    }
}
