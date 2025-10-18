<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invalid_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('optimization_result_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->string('rejection_reason');
            $table->json('task_details')->nullable();
            $table->timestamps();
            
            $table->index('task_id');
            $table->index('optimization_result_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invalid_tasks');
    }
};