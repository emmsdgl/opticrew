<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('optimization_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('optimization_generation_id');
            $table->integer('schedule_index'); // 0-19 (population of 20)
            $table->decimal('fitness_score', 8, 4);
            $table->json('team_assignments'); // Which tasks assigned to which teams
            $table->json('workload_distribution'); // Minutes per team
            $table->boolean('is_elite')->default(false);
            $table->boolean('is_final_result')->default(false);
            $table->string('created_by')->default('random'); // 'random', 'greedy', 'crossover', 'mutation'
            $table->timestamps();
            
            $table->foreign('optimization_generation_id')->references('id')->on('optimization_generations')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('optimization_schedules');
    }
};
