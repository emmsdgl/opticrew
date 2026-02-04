<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Task Reviews table stores feedback from company/manager users
     * about completed tasks for monitoring service quality
     */
    public function up()
    {
        Schema::create('task_reviews', function (Blueprint $table) {
            $table->id();

            // Task being reviewed
            $table->foreignId('task_id')
                ->constrained('tasks')
                ->onDelete('cascade');

            // Company/Client who submitted the review
            $table->foreignId('contracted_client_id')
                ->constrained('contracted_clients')
                ->onDelete('cascade');

            // User who submitted the review (company user)
            $table->foreignId('reviewer_user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Rating (1-5 scale)
            $table->tinyInteger('rating')->unsigned();

            // Feedback tags (JSON array of selected tags)
            // e.g., ["punctual", "professional", "thorough"]
            $table->json('feedback_tags')->nullable();

            // Detailed text review
            $table->text('review_text')->nullable();

            // Additional metadata for future analytics
            $table->json('metadata')->nullable();

            $table->timestamps();

            // Each task can only be reviewed once per company
            $table->unique(['task_id', 'contracted_client_id']);

            // Indexes for queries
            $table->index('rating');
            $table->index('contracted_client_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('task_reviews');
    }
};
