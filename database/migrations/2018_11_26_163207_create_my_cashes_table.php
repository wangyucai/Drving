<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMyCashesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('my_cashes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cash_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index();
            $table->string('points')->nullable();
            $table->integer('if_check')->unsigned()->default(0)->comment('提现是否被审核');
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
        Schema::dropIfExists('my_cashes');
    }
}
