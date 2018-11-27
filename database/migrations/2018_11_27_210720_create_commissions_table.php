<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type')->unsigned()->default(1)->comment('佣金类型');
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
        Schema::dropIfExists('commissions');
    }
}
