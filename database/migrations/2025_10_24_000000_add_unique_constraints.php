<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Check if a unique constraint exists
     */
    private function uniqueConstraintExists($table, $indexName)
    {
        $result = DB::select("
            SELECT COUNT(*) as count
            FROM INFORMATION_SCHEMA.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = ?
            AND INDEX_NAME = ?
        ", [$table, $indexName]);

        return $result[0]->count > 0;
    }

    /**
     * Run the migrations.
     *
     * Add unique constraints to prevent duplicate records and improve data integrity.
     *
     * @return void
     */
    public function up()
    {
        // Add unique constraint to day_offs table
        // Prevent same employee from having multiple day-off records for same date
        if (!$this->uniqueConstraintExists('day_offs', 'unique_employee_day_off')) {
            Schema::table('day_offs', function (Blueprint $table) {
                $table->unique(['employee_id', 'date'], 'unique_employee_day_off');
            });
        }

        // Add unique constraint to optimization_team_members table
        // Prevent same employee from being added to same team multiple times
        if (!$this->uniqueConstraintExists('optimization_team_members', 'unique_team_member')) {
            Schema::table('optimization_team_members', function (Blueprint $table) {
                $table->unique(['optimization_team_id', 'employee_id'], 'unique_team_member');
            });
        }

        // Add unique constraint to optimization_runs table
        // Note: MySQL/MariaDB does not support partial indexes with WHERE clauses
        // This feature requires PostgreSQL. Skipping for MySQL compatibility.
        // Duplicate prevention will be handled at application level in OptimizationService.php
        // See: https://dev.mysql.com/doc/refman/8.0/en/create-index.html

        // if (!$this->uniqueConstraintExists('optimization_runs', 'unique_unsaved_optimization_run')) {
        //     DB::statement('CREATE UNIQUE INDEX unique_unsaved_optimization_run ON optimization_runs (service_date) WHERE is_saved = false');
        // }

        // Add unique constraint to attendances table
        // Prevent duplicate clock-in records for same employee on same day
        if (!$this->uniqueConstraintExists('attendances', 'unique_employee_clock_in')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->unique(['employee_id', 'clock_in'], 'unique_employee_clock_in');
            });
        }

        // Add unique constraint to holidays table
        // Prevent duplicate holiday entries for the same date
        if (!$this->uniqueConstraintExists('holidays', 'unique_holiday_date')) {
            Schema::table('holidays', function (Blueprint $table) {
                $table->unique('date', 'unique_holiday_date');
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
        if ($this->uniqueConstraintExists('day_offs', 'unique_employee_day_off')) {
            Schema::table('day_offs', function (Blueprint $table) {
                $table->dropUnique('unique_employee_day_off');
            });
        }

        if ($this->uniqueConstraintExists('optimization_team_members', 'unique_team_member')) {
            Schema::table('optimization_team_members', function (Blueprint $table) {
                $table->dropUnique('unique_team_member');
            });
        }

        // Skipped: MySQL/MariaDB doesn't support partial indexes
        // if ($this->uniqueConstraintExists('optimization_runs', 'unique_unsaved_optimization_run')) {
        //     DB::statement('DROP INDEX IF EXISTS unique_unsaved_optimization_run');
        // }

        if ($this->uniqueConstraintExists('attendances', 'unique_employee_clock_in')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropUnique('unique_employee_clock_in');
            });
        }

        if ($this->uniqueConstraintExists('holidays', 'unique_holiday_date')) {
            Schema::table('holidays', function (Blueprint $table) {
                $table->dropUnique('unique_holiday_date');
            });
        }
    }
};
