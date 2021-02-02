<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id')->comment('申请人');
            $table->json('data')->nullable()->comment('审核信息');
            $table->string('remark')->nullable()->comment('备注');
            $table->unsignedInteger('examiner_id')->nullable()->comment('审核人');
            $table->tinyInteger('status')->default(0)->comment('状态：-1 - 被拒绝 0 - 待审批，1 - 已审批');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('certifications');
    }
}
