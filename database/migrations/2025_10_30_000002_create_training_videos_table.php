<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('training_videos', function (Blueprint $table) {
            $table->id();
            $table->string('category'); // cleaning_techniques, body_safety, hazard_prevention, chemical_safety
            $table->string('title');
            $table->string('title_fi')->nullable(); // Finnish title
            $table->text('description')->nullable();
            $table->text('description_fi')->nullable(); // Finnish description
            $table->string('video_id'); // YouTube video ID
            $table->string('platform')->default('youtube');
            $table->string('duration')->nullable(); // e.g., "5:30"
            $table->boolean('required')->default(false);
            $table->string('thumbnail_url')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create pivot table for tracking watched videos per employee
        Schema::create('employee_watched_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('training_video_id')->constrained()->onDelete('cascade');
            $table->timestamp('watched_at');
            $table->timestamps();

            $table->unique(['user_id', 'training_video_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_watched_videos');
        Schema::dropIfExists('training_videos');
    }
};
