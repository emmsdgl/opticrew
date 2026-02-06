<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Fix: Remove foreign key constraint on checklist_item_id since we're using item_index
     * instead of referencing a checklist_items table (which doesn't exist).
     */
    public function up(): void
    {
        // Only run if the table exists
        if (!Schema::hasTable('task_checklist_completions')) {
            return;
        }

        // Check if the foreign key constraint exists and drop it
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'task_checklist_completions'
            AND COLUMN_NAME = 'checklist_item_id'
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        if (!empty($foreignKeys)) {
            Schema::table('task_checklist_completions', function (Blueprint $table) use ($foreignKeys) {
                foreach ($foreignKeys as $fk) {
                    $table->dropForeign($fk->CONSTRAINT_NAME);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: Not restoring the foreign key since checklist_items table doesn't exist
        // and we intentionally removed it
    }
};
