<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Per-task rejection bookkeeping on the Task model itself.
        Schema::table('tasks', function (Blueprint $table) {
            // Reason an employee gave when they rejected this task (most recent).
            $table->string('rejection_reason', 500)->nullable()->after('reassignment_reason');
            // Number of times this task has been rejected. Used for the per-task ceiling (3).
            $table->unsignedTinyInteger('rejection_count')->default(0)->after('rejection_reason');
        });

        // The existing rejectTask endpoint was already writing status='Rejected',
        // but 'Rejected' is missing from the tasks.status ENUM, so the write either
        // silently truncated or errored depending on MySQL strict mode. Add it.
        DB::statement("ALTER TABLE tasks MODIFY COLUMN status "
            . "ENUM('Pending','Scheduled','In Progress','On Hold','Completed','Cancelled','Rejected') "
            . "NOT NULL DEFAULT 'Pending'");

        // Audit trail: one row per rejection event so we can:
        //   - count an employee's rejections in a time window (monthly budget)
        //   - reconstruct the full chain of who rejected what and why
        //   - power admin dashboards on rejection patterns
        Schema::create('task_rejections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('reason', 500);
            $table->timestamp('rejected_at')->useCurrent();
            $table->timestamps();

            // Index used by the budget check: rejections by employee in a date range.
            $table->index(['employee_id', 'rejected_at']);
            // Index used by the per-task ceiling check.
            $table->index(['task_id', 'rejected_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_rejections');

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['rejection_reason', 'rejection_count']);
        });

        // Restore the original ENUM (without 'Rejected'). Note: any rows with
        // status='Rejected' must be migrated/cancelled before rolling back.
        DB::statement("ALTER TABLE tasks MODIFY COLUMN status "
            . "ENUM('Pending','Scheduled','In Progress','On Hold','Completed','Cancelled') "
            . "NOT NULL DEFAULT 'Pending'");
    }
};
