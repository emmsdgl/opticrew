<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scenario_analyses', function (Blueprint $table) {
            $table->id();
            $table->date('service_date');
            $table->string('scenario_type');
            $table->json('parameters');
            $table->json('modified_schedule');
            $table->json('impact_analysis');
            $table->json('recommendations')->nullable();
            $table->timestamps();
            
            $table->index(['service_date', 'scenario_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scenario_analyses');
    }
};