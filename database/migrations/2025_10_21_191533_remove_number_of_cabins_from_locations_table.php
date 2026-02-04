<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('locations', 'number_of_cabins')) {
            Schema::table('locations', function (Blueprint $table) {
                $table->dropColumn('number_of_cabins');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->integer('number_of_cabins')->nullable()->after('student_sunday_holiday_rate')->comment('Number of cabins of this type');
        });
    }
};
