<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('performance_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('evaluator_id')->constrained('users')->onDelete('cascade');
            $table->date('evaluation_period_start');
            $table->date('evaluation_period_end');
            $table->enum('status', ['draft', 'completed', 'acknowledged'])->default('draft');

            // Criteria scores (1-5 scale)
            $table->unsignedTinyInteger('attendance_score')->nullable();
            $table->unsignedTinyInteger('punctuality_score')->nullable();
            $table->unsignedTinyInteger('task_completion_score')->nullable();
            $table->unsignedTinyInteger('quality_of_work_score')->nullable();
            $table->unsignedTinyInteger('professionalism_score')->nullable();
            $table->unsignedTinyInteger('teamwork_score')->nullable();

            // Overall
            $table->decimal('overall_rating', 3, 2)->nullable();

            // Feedback text fields
            $table->text('strengths')->nullable();
            $table->text('areas_for_improvement')->nullable();
            $table->text('goals_for_next_period')->nullable();
            $table->text('admin_comments')->nullable();

            // Auto-fill data snapshot (JSON of system metrics used)
            $table->json('system_metrics')->nullable();

            $table->boolean('requires_pip')->default(false);
            $table->timestamps();

            $table->index(['employee_id', 'evaluation_period_start'], 'perf_eval_emp_period_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('performance_evaluations');
    }
};