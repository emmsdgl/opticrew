<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fix: tasks.assigned_team_id was referencing daily_team_assignments (old/unused table).
     * It should reference optimization_teams, which is where the optimization service
     * stores team assignments. Also drops legacy unused tables.
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign('tasks_assigned_team_id_foreign');
            $table->foreign('assigned_team_id')
                  ->references('id')
                  ->on('optimization_teams')
                  ->onDelete('set null');
        });

        // Drop legacy tables that are no longer used
        Schema::dropIfExists('team_members');
        Schema::dropIfExists('daily_team_assignments');
    }

    public function down()
    {
        Schema::create('daily_team_assignments', function (Blueprint $table) {
            $table->id();
            $table->date('assignment_date');
            $table->foreignId('car_id')->nullable()->constrained('cars');
            $table->foreignId('contracted_client_id')->constrained('contracted_clients');
            $table->timestamps();
        });

        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_team_id')->constrained('daily_team_assignments');
            $table->foreignId('employee_id')->constrained('employees');
            $table->timestamps();
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['assigned_team_id']);
            $table->foreign('assigned_team_id')
                  ->references('id')
                  ->on('daily_team_assignments');
        });
    }
};
