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
            $table->string('username')->comment('用户名');
            $table->string('email')->nullable()->unique()->comment('邮箱');
            $table->string('password')->comment('密码');
            $table->string('personal_name')->comment('个人名称');
            $table->string('drive_school_name')->comment('驾校名称');
            $table->string('registration_site')->comment('报名地点');
            $table->string('trainingground_site')->comment('训练场地点');
            $table->string('class_introduction')->comment('班别介绍');
            $table->rememberToken();
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
