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
        // Add is_saved field to optimization_runs table
        Schema::table('optimization_runs', function (Blueprint $table) {
            if (!Schema::hasColumn('optimization_runs', 'is_saved')) {
                $table->boolean('is_saved')->default(false)->after('status');
            }
        });

        // Add new fields to tasks table
        Schema::table('tasks', function (Blueprint $table) {
            // Real-time status tracking
            if (!Schema::hasColumn('tasks', 'on_hold_reason')) {
                $table->string('on_hold_reason')->nullable()->after('status');
            }
            if (!Schema::hasColumn('tasks', 'on_hold_timestamp')) {
                $table->timestamp('on_hold_timestamp')->nullable()->after('on_hold_reason');
            }

            // Performance tracking
            if (!Schema::hasColumn('tasks', 'actual_duration')) {
                $table->integer('actual_duration')->nullable()->after('estimated_duration_minutes')
                    ->comment('Actual time taken in minutes (auto-calculated)');
            }
            if (!Schema::hasColumn('tasks', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('started_at');
            }

            // Reassignment tracking
            if (!Schema::hasColumn('tasks', 'reassigned_at')) {
                $table->timestamp('reassigned_at')->nullable()->after('assigned_team_id');
            }
            if (!Schema::hasColumn('tasks', 'reassignment_reason')) {
                $table->text('reassignment_reason')->nullable()->after('reassigned_at');
            }

            // Arrival status for priority (RULE 3)
            if (!Schema::hasColumn('tasks', 'arrival_status')) {
                $table->boolean('arrival_status')->default(false)->after('task_description')
                    ->comment('TRUE if guest arriving, FALSE otherwise');
            }
        });

        // Create alerts table for admin notifications
        if (!Schema::hasTable('alerts')) {
            Schema::create('alerts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade');
                $table->string('alert_type'); // 'task_delayed', 'duration_exceeded', etc.
                $table->integer('delay_minutes')->nullable();
                $table->text('reason')->nullable();
                $table->timestamp('triggered_at');
                $table->timestamp('acknowledged_at')->nullable();
                $table->foreignId('acknowledged_by')->nullable()->constrained('users');
                $table->timestamps();

                $table->index(['task_id', 'alert_type']);
            });
        }

        // Create performance_flags table
        if (!Schema::hasTable('performance_flags')) {
            Schema::create('performance_flags', function (Blueprint $table) {
                $table->id();
                $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade');
                $table->foreignId('employee_id')->nullable()->constrained('employees')->onDelete('set null');
                $table->foreignId('team_id')->nullable()->comment('Team ID from optimization_teams');
                $table->string('flag_type'); // 'duration_exceeded', 'quality_issue', etc.
                $table->integer('estimated_minutes')->nullable();
                $table->integer('actual_minutes')->nullable();
                $table->integer('variance_minutes')->nullable();
                $table->timestamp('flagged_at');
                $table->boolean('reviewed')->default(false);
                $table->foreignId('reviewed_by')->nullable()->constrained('users');
                $table->timestamp('reviewed_at')->nullable();
                $table->text('review_notes')->nullable();
                $table->timestamps();

                $table->index(['task_id', 'flag_type']);
                $table->index('reviewed');
            });
        }

        // Create employee_performance table for nightly reconciliation
        if (!Schema::hasTable('employee_performance')) {
            Schema::create('employee_performance', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
                $table->date('date');
                $table->integer('tasks_completed')->default(0);
                $table->decimal('total_performance_score', 8, 4)->default(0);
                $table->decimal('average_performance', 8, 4)->default(0)
                    ->comment('Score > 1.0 = faster, < 1.0 = slower');
                $table->timestamps();

                $table->unique(['employee_id', 'date']);
                $table->index('date');
            });
        }

        // Add base_cleaning_duration_minutes to locations table if not exists
        if (!Schema::hasColumn('locations', 'base_cleaning_duration_minutes')) {
            Schema::table('locations', function (Blueprint $table) {
                $table->integer('base_cleaning_duration_minutes')->default(60)
                    ->comment('Base duration for cleaning this location type');
            });
        }

        // Add months_employed to employees table for efficiency calculation
        if (!Schema::hasColumn('employees', 'months_employed')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->integer('months_employed')->default(0)
                    ->comment('Months of employment for efficiency calculation');
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
        Schema::table('optimization_runs', function (Blueprint $table) {
            $table->dropColumn('is_saved');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn([
                'on_hold_reason',
                'on_hold_timestamp',
                'actual_duration',
                'completed_at',
                'reassigned_at',
                'reassignment_reason',
                'arrival_status'
            ]);
        });

        Schema::dropIfExists('alerts');
        Schema::dropIfExists('performance_flags');
        Schema::dropIfExists('employee_performance');

        if (Schema::hasColumn('locations', 'base_cleaning_duration_minutes')) {
            Schema::table('locations', function (Blueprint $table) {
                $table->dropColumn('base_cleaning_duration_minutes');
            });
        }

        if (Schema::hasColumn('employees', 'months_employed')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropColumn('months_employed');
            });
        }
    }
};
