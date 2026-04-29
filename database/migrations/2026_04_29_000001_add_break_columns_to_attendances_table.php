<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->timestamp('lunch_break_start')->nullable()->after('clock_out');
            $table->timestamp('lunch_break_end')->nullable()->after('lunch_break_start');
            $table->string('lunch_break_status', 20)->nullable()->after('lunch_break_end');

            $table->timestamp('dinner_break_start')->nullable()->after('lunch_break_status');
            $table->timestamp('dinner_break_end')->nullable()->after('dinner_break_start');
            $table->string('dinner_break_status', 20)->nullable()->after('dinner_break_end');
        });
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'lunch_break_start',
                'lunch_break_end',
                'lunch_break_status',
                'dinner_break_start',
                'dinner_break_end',
                'dinner_break_status',
            ]);
        });
    }
};
