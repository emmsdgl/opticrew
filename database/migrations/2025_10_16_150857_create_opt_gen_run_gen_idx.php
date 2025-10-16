<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('optimization_generations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('optimization_run_id');
            $table->integer('generation_number');
            $table->decimal('best_fitness', 8, 4);
            $table->decimal('average_fitness', 8, 4);
            $table->decimal('worst_fitness', 8, 4);
            $table->boolean('is_improvement')->default(false);
            $table->json('best_schedule_data'); // The best schedule of this generation
            $table->json('population_summary')->nullable(); // Summary of all 20 schedules
            $table->timestamps();
            
            $table->foreign('optimization_run_id')->references('id')->on('optimization_runs')->onDelete('cascade');
            $table->index(['optimization_run_id', 'generation_number'], 'opt_gen_run_gen_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('optimization_generations');
    }
};