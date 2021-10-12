<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('products')) {
            Schema::drop('products');
        }
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id')->index()->comment('店家用户id');
            $table->unsignedInteger('store_id')->index()->comment('商店id');
            $table->String('name')->comment("商品名称");
            $table->text('description')->comment("商品描述");
            $table->unsignedInteger('price')->comment('商品价格');

            $table->unsignedInteger('category_id')->nullable()->index()->comment('商品分类');
            $table->unsignedInteger('parent_id')->index()->nullable()->comment('父商品，子产品可在规格价格方面差异');
            $table->Integer('video_id')->nullable()->index()->comment('视频id');
            $table->string('address')->nullable()->comment('位置地名');
            $table->unsignedInteger('valid_day')->nullable();
            $table->double('service_duration')->nullable()->comment('服务时长');

            $table->string('phone')->nullable();

            //用于出售任何平台的账号商品
            // $table->json("platform_acounts")->index()->nullable()->comment("所有账号：[{platform:平台，account:账号，password:密码}]");

            $table->unsignedInteger('available_amount')->nullable()->comment('商品上架中数量,null:无限');
            $table->unsignedInteger('amount')->nullable()->comment('商品总数量,null:无限');

            //TODO: 租号时间不同规格时：parent_id 子产品关系, 子产品需要规格字段
            $table->String('dimension')->nullable()->index()->comment("规格维度");
            $table->String('dimension2')->nullable()->index()->comment("规格维度2");

            $table->string('location')->index()->nullable()->comment('位置信息');
            $table->Integer('status')->default(0)->comment("0:默认 1:上架 -1:下架");
            $table->double('service_duration')->nullable()->comment('服务时长');
            $table->double('open_time_at')->nullable()->comment('开放时间');
            $table->double('close_time_at')->nullable()->comment('失效时间');
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
        Schema::dropIfExists('products');
    }
}
