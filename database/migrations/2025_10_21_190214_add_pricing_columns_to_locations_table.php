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
        Schema::table('locations', function (Blueprint $table) {
            // Add pricing columns
            $table->decimal('normal_rate_per_hour', 10, 2)->nullable()->after('base_cleaning_duration_minutes');
            $table->decimal('sunday_holiday_rate', 10, 2)->nullable()->after('normal_rate_per_hour');
            $table->decimal('deep_cleaning_rate', 10, 2)->nullable()->after('sunday_holiday_rate');
            $table->decimal('light_deep_cleaning_rate', 10, 2)->nullable()->after('deep_cleaning_rate');
            $table->decimal('student_rate', 10, 2)->nullable()->after('light_deep_cleaning_rate');
            $table->decimal('student_sunday_holiday_rate', 10, 2)->nullable()->after('student_rate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn([
                'normal_rate_per_hour',
                'sunday_holiday_rate',
                'deep_cleaning_rate',
                'light_deep_cleaning_rate',
                'student_rate',
                'student_sunday_holiday_rate'
            ]);
        });
    }
};
