<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('optimization_runs', function (Blueprint $table) {
            $table->id();
            $table->date('service_date');
            $table->unsignedBigInteger('triggered_by_task_id')->nullable();
            $table->enum('status', ['running', 'completed', 'failed'])->default('running');
            $table->integer('total_tasks');
            $table->integer('total_teams');
            $table->integer('total_employees');
            $table->json('employee_allocation_data')->nullable(); // Rule-based phase
            $table->json('greedy_result_data')->nullable(); // Greedy phase
            $table->decimal('final_fitness_score', 8, 4)->nullable();
            $table->integer('generations_run')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamps();
            
            $table->foreign('triggered_by_task_id')->references('id')->on('tasks')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('optimization_runs');
    }
};
