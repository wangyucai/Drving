<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSendMsgsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('send_msgs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type')->unsigned()->default(1)->comment('类型,1:学员预约');
            $table->integer('user_id')->unsigned()->index();
            $table->string('form_id');
            $table->string('keyword1')->nullable();
            $table->string('keyword2')->nullable();
            $table->string('keyword3')->nullable();
            $table->string('keyword4')->nullable();
            $table->integer('if_send')->unsigned()->default(0)->comment('是否发送成功');
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
        Schema::dropIfExists('send_msgs');
    }
}
