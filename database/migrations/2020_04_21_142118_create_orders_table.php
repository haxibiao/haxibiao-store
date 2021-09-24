<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('store_id')->index()->nullable()->comment('关联店铺');
            $table->unsignedInteger('technician_id')->index()->nullable()->comment('关联技师');
            $table->unsignedInteger('recharge_id')->index()->nullable();
            $table->timestamp('appointment_time')->nullable()->comment('预约时间');
            $table->String("account")->nullable()->comment("下单时的账号");
            $table->String("password")->nullable()->comment("下单时的密码");
            //用作nova后台展示订单关联账号
            $table->String('platformAccount_id')->nullable()->index()->comment("订单账号");
            $table->String('number')->index()->comment("订单号");
            $table->Integer('status')->comment("订单状态：0:未支付, 1:已支付|可用中, 2:已收货, 3：已过期(账号时间到)");
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
        Schema::dropIfExists('orders');
    }
}
