<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates optimization_teams and optimization_team_members tables
     * These replace the old daily_team_assignments and team_members tables
     *
     * @return void
     */
    public function up()
    {
        // Create optimization_teams table
        if (!Schema::hasTable('optimization_teams')) {
            Schema::create('optimization_teams', function (Blueprint $table) {
                $table->id();
                $table->foreignId('optimization_run_id')
                    ->constrained('optimization_runs')
                    ->onDelete('cascade');
                $table->integer('team_index')->comment('Team number (1, 2, 3, etc.)');
                $table->date('service_date');
                $table->foreignId('car_id')->nullable()->constrained('cars')->onDelete('set null');
                $table->foreignId('what_if_scenario_id')->nullable()
                    ->comment('NULL for baseline, ID for what-if scenarios');
                $table->timestamps();

                $table->index(['optimization_run_id', 'service_date']);
                $table->index('what_if_scenario_id');
            });
        }

        // Create optimization_team_members table
        if (!Schema::hasTable('optimization_team_members')) {
            Schema::create('optimization_team_members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('optimization_team_id')
                    ->constrained('optimization_teams')
                    ->onDelete('cascade');
                $table->foreignId('employee_id')
                    ->constrained('employees')
                    ->onDelete('cascade');
                $table->timestamps();

                $table->unique(['optimization_team_id', 'employee_id'], 'team_employee_unique');
                $table->index('employee_id');
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
        Schema::dropIfExists('optimization_team_members');
        Schema::dropIfExists('optimization_teams');
    }
};
