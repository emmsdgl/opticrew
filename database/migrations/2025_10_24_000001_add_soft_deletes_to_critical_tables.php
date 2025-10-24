<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add soft delete support to critical tables for audit trail and data recovery.
     *
     * @return void
     */
    public function up()
    {
        // Add soft deletes to users table
        if (!Schema::hasColumn('users', 'deleted_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to employees table
        if (!Schema::hasColumn('employees', 'deleted_at')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to tasks table
        if (!Schema::hasColumn('tasks', 'deleted_at')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to optimization_runs table
        if (!Schema::hasColumn('optimization_runs', 'deleted_at')) {
            Schema::table('optimization_runs', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to clients table
        if (!Schema::hasColumn('clients', 'deleted_at')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to contracted_clients table
        if (!Schema::hasColumn('contracted_clients', 'deleted_at')) {
            Schema::table('contracted_clients', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to locations table
        if (!Schema::hasColumn('locations', 'deleted_at')) {
            Schema::table('locations', function (Blueprint $table) {
                $table->softDeletes();
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
        if (Schema::hasColumn('users', 'deleted_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('employees', 'deleted_at')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('tasks', 'deleted_at')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('optimization_runs', 'deleted_at')) {
            Schema::table('optimization_runs', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('clients', 'deleted_at')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('contracted_clients', 'deleted_at')) {
            Schema::table('contracted_clients', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('locations', 'deleted_at')) {
            Schema::table('locations', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
