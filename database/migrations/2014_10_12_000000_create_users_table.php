<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('phone')->unique()->comment('手机号');
            $table->string('carno')->nullable()->unique()->comment('身份证号');
            $table->string('name')->nullable()->comment('姓名');
            $table->enum('type', ['trainer', 'student'])->comment('会员类型');
            $table->unsignedInteger('f_uid')->nullable()->comment('所属教练');
            $table->string('car_number')->nullable()->comment('车牌号码');
            $table->string('registration_site')->nullable()->comment('报名地点');
            $table->string('trainingground_site')->nullable()->comment('训练场地点');
            $table->string('weapp_openid')->nullable()->unique()->comment('openid');
            $table->string('weixin_session_key')->nullable()->comment('key');
            $table->string('all_time')->nullable()->comment('教练全部时间段');
            $table->unsignedInteger('single_time')->nullable()->comment('教练单个时间段');
            $table->unsignedInteger('day_times')->nullable()->comment('次数');
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
        Schema::dropIfExists('users');
    }
}
