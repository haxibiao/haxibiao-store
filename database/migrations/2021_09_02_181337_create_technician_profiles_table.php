<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTechnicianProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('technician_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->unique()->index()->comment('用户ID');
            $table->unsignedInteger('store_id')->index()->comment('商户ID');
            $table->unsignedSmallInteger('rating')->nullable()->comment('评级、星级');
            $table->unsignedInteger('number')->uniqid()->index()->comment('编号');
            $table->unsignedInteger('serve_count')->default(0)->comment('服务次数');
            $table->unsignedSmallInteger('status')->default(0)->comment('技师状态：0未上班|2空闲中|1服务中');
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
        Schema::dropIfExists('technician_profiles');
    }
}
