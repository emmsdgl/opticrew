<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Drop unused/deprecated tables identified in database audit:
     * - scheduling_logs: Never implemented
     * - payroll_reports: Never implemented
     * - task_performance_histories: Replaced by employee_performance table
     * - employee_schedules: Replaced by day_offs table
     *
     * Note: daily_team_assignments and team_members are NOT dropped yet
     * as they may contain legacy data that needs migration to
     * optimization_teams and optimization_team_members first.
     *
     * @return void
     */
    public function up()
    {
        // Drop tables that were never used
        Schema::dropIfExists('scheduling_logs');
        Schema::dropIfExists('payroll_reports');

        // Drop deprecated tables (replaced by newer implementations)
        Schema::dropIfExists('task_performance_histories');
        Schema::dropIfExists('employee_schedules');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Recreate scheduling_logs table
        Schema::create('scheduling_logs', function (Blueprint $table) {
            $table->id();
            $table->text('log_message');
            $table->timestamp('logged_at');
            $table->timestamps();
        });

        // Recreate payroll_reports table
        Schema::create('payroll_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->date('report_date');
            $table->decimal('total_amount', 10, 2);
            $table->timestamps();
        });

        // Recreate task_performance_histories table
        Schema::create('task_performance_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->integer('estimated_duration_minutes');
            $table->integer('actual_duration_minutes');
            $table->timestamp('completed_at');
            $table->timestamps();
        });

        // Recreate employee_schedules table
        Schema::create('employee_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->date('work_date');
            $table->boolean('is_day_off')->default(false);
            $table->timestamps();
            $table->unique(['employee_id', 'work_date']);
        });
    }
};
