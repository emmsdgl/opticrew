<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skill_embeddings', function (Blueprint $table) {
            $table->id();
            $table->string('text');
            $table->string('text_normalized')->index();
            $table->longText('embedding'); // Serialized 384-dim float vector
            $table->string('source')->default('general'); // 'job_posting' or 'applicant'
            $table->unsignedBigInteger('source_id')->nullable();
            $table->timestamps();

            $table->index(['source', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skill_embeddings');
    }
};
