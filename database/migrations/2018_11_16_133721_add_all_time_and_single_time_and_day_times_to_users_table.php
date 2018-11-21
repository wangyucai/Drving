<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAllTimeAndSingleTimeAndDayTimesToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('all_time')->nullable()->after('class_introduction');
            $table->unsignedInteger('single_time')->nullable()->after('all_time');
            $table->unsignedInteger('day_times')->nullable()->after('single_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('all_time');
            $table->dropColumn('single_time');
            $table->dropColumn('day_times');
        });
    }
}
