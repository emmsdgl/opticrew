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
        // Add indexes to tasks table (most critical for performance)
        Schema::table('tasks', function (Blueprint $table) {
            $table->index('scheduled_date', 'idx_tasks_scheduled_date');
            $table->index('deleted_at', 'idx_tasks_deleted_at');
            $table->index('assigned_team_id', 'idx_tasks_assigned_team_id');
            $table->index('location_id', 'idx_tasks_location_id');
            $table->index('client_id', 'idx_tasks_client_id');
            $table->index(['scheduled_date', 'deleted_at'], 'idx_tasks_scheduled_deleted');
        });

        // Add indexes to users table
        Schema::table('users', function (Blueprint $table) {
            $table->index('role', 'idx_users_role');
            $table->index('deleted_at', 'idx_users_deleted_at');
            $table->index(['role', 'deleted_at'], 'idx_users_role_deleted');
        });

        // Add indexes to employees table
        Schema::table('employees', function (Blueprint $table) {
            $table->index('user_id', 'idx_employees_user_id');
            $table->index('deleted_at', 'idx_employees_deleted_at');
            $table->index('is_active', 'idx_employees_is_active');
        });

        // Add indexes to optimization_teams table
        Schema::table('optimization_teams', function (Blueprint $table) {
            $table->index('optimization_run_id', 'idx_opt_teams_run_id');
        });

        // Add indexes to optimization_team_members table
        Schema::table('optimization_team_members', function (Blueprint $table) {
            $table->index('optimization_team_id', 'idx_opt_members_team_id');
            $table->index('employee_id', 'idx_opt_members_employee_id');
        });

        // Add indexes to client_appointments table
        Schema::table('client_appointments', function (Blueprint $table) {
            $table->index('service_date', 'idx_appointments_service_date');
            $table->index('client_id', 'idx_appointments_client_id');
            $table->index('status', 'idx_appointments_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop indexes from tasks table
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('idx_tasks_scheduled_date');
            $table->dropIndex('idx_tasks_deleted_at');
            $table->dropIndex('idx_tasks_assigned_team_id');
            $table->dropIndex('idx_tasks_location_id');
            $table->dropIndex('idx_tasks_client_id');
            $table->dropIndex('idx_tasks_scheduled_deleted');
        });

        // Drop indexes from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_role');
            $table->dropIndex('idx_users_deleted_at');
            $table->dropIndex('idx_users_role_deleted');
        });

        // Drop indexes from employees table
        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex('idx_employees_user_id');
            $table->dropIndex('idx_employees_deleted_at');
            $table->dropIndex('idx_employees_is_active');
        });

        // Drop indexes from optimization_teams table
        Schema::table('optimization_teams', function (Blueprint $table) {
            $table->dropIndex('idx_opt_teams_run_id');
        });

        // Drop indexes from optimization_team_members table
        Schema::table('optimization_team_members', function (Blueprint $table) {
            $table->dropIndex('idx_opt_members_team_id');
            $table->dropIndex('idx_opt_members_employee_id');
        });

        // Drop indexes from client_appointments table
        Schema::table('client_appointments', function (Blueprint $table) {
            $table->dropIndex('idx_appointments_service_date');
            $table->dropIndex('idx_appointments_client_id');
            $table->dropIndex('idx_appointments_status');
        });
    }
};
