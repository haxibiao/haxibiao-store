<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductIdToImageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('images', function (Blueprint $table) {
            //方便nova后台上传相关图片，多对多查询全部图片建立关联，数据库会吃不消
            if (!Schema::hasColumn('images', 'store_id')) {
                $table->Integer('store_id')->index()->comment('店铺id');
            }
            if (!Schema::hasColumn('images', 'product_id')) {
                $table->Integer('product_id')->index()->comment('商品id');
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
        Schema::table('image', function (Blueprint $table) {
            //
        });
    }
}
