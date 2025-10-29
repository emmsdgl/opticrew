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
        Schema::table('attendances', function (Blueprint $table) {
            $table->decimal('clock_in_latitude', 10, 8)->nullable()->after('clock_out');
            $table->decimal('clock_in_longitude', 11, 8)->nullable()->after('clock_in_latitude');
            $table->decimal('clock_out_latitude', 10, 8)->nullable()->after('clock_in_longitude');
            $table->decimal('clock_out_longitude', 11, 8)->nullable()->after('clock_out_latitude');
            $table->decimal('clock_in_distance', 8, 2)->nullable()->after('clock_out_longitude')->comment('Distance in meters from office');
            $table->decimal('clock_out_distance', 8, 2)->nullable()->after('clock_in_distance')->comment('Distance in meters from office');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'clock_in_latitude',
                'clock_in_longitude',
                'clock_out_latitude',
                'clock_out_longitude',
                'clock_in_distance',
                'clock_out_distance'
            ]);
        });
    }
};
