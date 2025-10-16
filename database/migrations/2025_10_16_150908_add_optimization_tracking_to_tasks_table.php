<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedBigInteger('optimization_run_id')->nullable()->after('assigned_team_id');
            $table->integer('assigned_by_generation')->nullable()->after('optimization_run_id');
            
            $table->foreign('optimization_run_id')->references('id')->on('optimization_runs')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['optimization_run_id']);
            $table->dropColumn(['optimization_run_id', 'assigned_by_generation']);
        });
    }
};
