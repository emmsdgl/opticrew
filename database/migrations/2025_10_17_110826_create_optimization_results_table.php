<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('optimization_results', function (Blueprint $table) {
            $table->id();
            $table->date('service_date');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->json('schedule');
            $table->decimal('fitness_score', 5, 3);
            $table->integer('generation_count')->default(0);
            $table->timestamps();
            
            $table->index(['service_date', 'client_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('optimization_results');
    }
};