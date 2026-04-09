<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds GA-computed clock times to each task.
     *
     * After the optimizer assigns tasks to teams and decides their order,
     * it walks each team's queue and computes a real start/end clock time
     * (in minutes since midnight). The Schedule UI uses these to show the
     * actual planned timeline instead of every task saying "08:00".
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedSmallInteger('optimized_start_minutes')->nullable()->after('scheduled_time');
            $table->unsignedSmallInteger('optimized_end_minutes')->nullable()->after('optimized_start_minutes');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['optimized_start_minutes', 'optimized_end_minutes']);
        });
    }
};
