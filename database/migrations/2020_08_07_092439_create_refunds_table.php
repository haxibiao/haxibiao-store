<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->integer("order_id")->comment("关联订单");
            $table->text('content')
                ->nullable()
                ->comment('留言');

            $table->unsignedInteger('user_id')
                ->nullable()
                ->index()
                ->comment('用户');

            $table->unsignedInteger('status')->default(0)->comment('0待处理 1已驳回 2已处理');
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
        Schema::dropIfExists('refunds');
    }
}
