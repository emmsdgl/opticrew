<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('optimization_teams', function (Blueprint $table) {
            $table->enum('staffing_status', ['fully_staffed', 'incomplete_staffing'])
                ->default('fully_staffed')
                ->after('car_id');
        });
    }

    public function down(): void
    {
        Schema::table('optimization_teams', function (Blueprint $table) {
            $table->dropColumn('staffing_status');
        });
    }
};
