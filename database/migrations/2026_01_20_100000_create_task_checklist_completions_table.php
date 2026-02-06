<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tracks which checklist items have been completed for each task
     */
    public function up(): void
    {
        Schema::create('task_checklist_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            // Using item_index instead of foreign key since checklist items are defined in templates
            $table->unsignedBigInteger('checklist_item_id'); // Stores the item index from the template array
            $table->boolean('is_completed')->default(false);
            $table->foreignId('completed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Ensure each task-item combination is unique
            $table->unique(['task_id', 'checklist_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_checklist_completions');
    }
};
