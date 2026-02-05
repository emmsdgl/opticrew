<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'started_by')) {
                $table->unsignedBigInteger('started_by')->nullable()->after('employee_approved_at');
                $table->foreign('started_by')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('tasks', 'started_at')) {
                $table->timestamp('started_at')->nullable()->after('employee_approved_at');
            }
            if (!Schema::hasColumn('tasks', 'completed_by')) {
                $table->unsignedBigInteger('completed_by')->nullable()->after('employee_approved_at');
                $table->foreign('completed_by')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('tasks', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('employee_approved_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['started_by']);
            $table->dropForeign(['completed_by']);
            $table->dropColumn(['started_by', 'started_at', 'completed_by', 'completed_at']);
        });
    }
};
