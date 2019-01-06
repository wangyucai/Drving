<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrainerTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trainer_times', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('schedule_id')->unsigned()->comment('学车时间段ID');
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
        Schema::dropIfExists('trainer_times');
    }
}
