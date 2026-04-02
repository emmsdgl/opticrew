<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('performance_improvement_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('evaluation_id')->nullable()->constrained('performance_evaluations')->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('areas_to_improve'); // [{area, details}]
            $table->json('action_items');     // [{description, target_date, status}]
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['active', 'completed', 'extended', 'cancelled'])->default('active');
            $table->text('outcome_notes')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('performance_improvement_plans');
    }
};