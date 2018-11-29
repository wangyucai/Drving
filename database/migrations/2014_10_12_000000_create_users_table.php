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
            $table->string('username')->nullable()->comment('用户名');
            $table->string('avatar')->nullable()->comment('头像');
            $table->string('introduction')->nullable()->comment('个人简介');
            $table->integer('if_check')->unsigned()->default(0)->comment('教练是否审核');
            $table->enum('type', ['student', 'trainer'])->comment('会员类型');
            $table->unsignedInteger('my_points')->default(0)->comment('我的积分');
            $table->unsignedInteger('subject')->default(1)->comment('学员科目');
            $table->unsignedInteger('f_uid')->nullable()->comment('所属教练');
            $table->string('car_number')->nullable()->comment('教练车牌号码');
            $table->string('registration_site')->nullable()->comment('学员报名地点');
            $table->string('trainingground_site')->nullable()->comment('学员练车地点');
            $table->text('car_photo')->nullable()->comment('教练车辆照片');
            $table->string('weapp_openid')->nullable()->unique()->comment('openid');
            $table->string('weixin_session_key')->nullable()->comment('key');
            $table->string('all_time')->nullable()->comment('教练全部时间段');
            $table->unsignedInteger('single_time')->nullable()->comment('教练单个时间段');
            $table->unsignedInteger('day_times')->nullable()->comment('次数');
            $table->unsignedInteger('parent_id')->nullable()->comment('我的上级');
            $table->foreign('parent_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedInteger('level')->comment('当前分销的层级');
            $table->string('path')->comment('该分销所有父级分销 id');
            $table->boolean('is_get_cash')->default(false)->comment('所有上级是否拿到佣金,教练无限极，学员三级');
            $table->decimal('one_level')->default(0.00)->comment('一级佣金');
            $table->decimal('two_level')->default(0.00)->comment('二级佣金');
            $table->decimal('three_level')->default(0.00)->comment('三级佣金');
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
