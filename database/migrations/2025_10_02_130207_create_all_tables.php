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
        // Inside the up() function of your create_all_tables.php migration file

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'employee', 'external_client']);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('full_name');
            $table->json('skills');
            $table->timestamps();
        });

        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('client_type', ['personal', 'company']);
            $table->string('company_name')->nullable();
            $table->timestamps();
        });

        Schema::create('contracted_clients', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contracted_client_id')->constrained()->onDelete('cascade');
            $table->string('location_name');
            $table->string('location_type');
            $table->integer('base_cleaning_duration_minutes');
            $table->timestamps();
        });

        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('car_name');
            $table->boolean('is_available')->default(true);
            $table->timestamps(); // <-- ADD THIS LINE
        });

        Schema::create('daily_team_assignments', function (Blueprint $table) {
            $table->id();
            $table->date('assignment_date');
            $table->foreignId('car_id')->nullable()->constrained();
            $table->foreignId('contracted_client_id')->constrained();
            $table->timestamps();
        });

        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_team_id')->constrained('daily_team_assignments')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->timestamps(); // <-- ADD THIS LINE
        });

        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->nullable()->constrained();
            $table->foreignId('client_id')->nullable()->constrained();
            $table->text('task_description');
            $table->integer('estimated_duration_minutes');
            $table->date('scheduled_date');
            $table->enum('status', ['Pending', 'Scheduled', 'In-Progress', 'Completed', 'Cancelled'])->default('Pending');
            $table->foreignId('assigned_team_id')->nullable()->constrained('daily_team_assignments');
            $table->timestamp('started_at')->nullable(); // <-- ADD THIS LINE
            $table->timestamps();
        });

        Schema::create('employee_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->date('work_date');
            $table->boolean('is_day_off')->default(false);
            $table->timestamps();
            $table->unique(['employee_id', 'work_date']);
        });

        Schema::create('task_performance_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->integer('estimated_duration_minutes');
            $table->integer('actual_duration_minutes');
            $table->timestamp('completed_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('all_tables');
    }
};
