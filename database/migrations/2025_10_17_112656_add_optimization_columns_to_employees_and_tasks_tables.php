<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add new columns to employees table
        Schema::table('employees', function (Blueprint $table) {
            // Check if columns don't exist before adding
            if (!Schema::hasColumn('employees', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('skills');
            }
            if (!Schema::hasColumn('employees', 'is_day_off')) {
                $table->boolean('is_day_off')->default(false)->after('is_active');
            }
            if (!Schema::hasColumn('employees', 'is_busy')) {
                $table->boolean('is_busy')->default(false)->after('is_day_off');
            }
            if (!Schema::hasColumn('employees', 'efficiency')) {
                $table->decimal('efficiency', 3, 2)->default(1.00)->after('is_busy')->comment('Employee efficiency multiplier (0.5 to 2.0)');
            }
            if (!Schema::hasColumn('employees', 'has_driving_license')) {
                $table->boolean('has_driving_license')->default(false)->after('efficiency');
            }
            if (!Schema::hasColumn('employees', 'years_of_experience')) {
                $table->integer('years_of_experience')->default(0)->after('has_driving_license');
            }
        });

        // Add new columns to tasks table
        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'scheduled_time')) {
                $table->time('scheduled_time')->nullable()->after('scheduled_date');
            }
            if (!Schema::hasColumn('tasks', 'duration')) {
                $table->integer('duration')->nullable()->after('scheduled_time')->comment('Task duration in minutes');
            }
            if (!Schema::hasColumn('tasks', 'travel_time')) {
                $table->integer('travel_time')->default(0)->after('duration')->comment('Travel time to location in minutes');
            }
            if (!Schema::hasColumn('tasks', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable()->after('travel_time');
            }
            if (!Schema::hasColumn('tasks', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            }
            if (!Schema::hasColumn('tasks', 'required_equipment')) {
                $table->json('required_equipment')->nullable()->after('longitude');
            }
            if (!Schema::hasColumn('tasks', 'required_skills')) {
                $table->json('required_skills')->nullable()->after('required_equipment');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'is_active',
                'is_day_off',
                'is_busy',
                'efficiency',
                'has_driving_license',
                'years_of_experience',
            ]);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn([
                'scheduled_time',
                'duration',
                'travel_time',
                'latitude',
                'longitude',
                'required_equipment',
                'required_skills',
            ]);
        });
    }
};