<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->boolean('is_late')->default(false)->after('status');
            $table->integer('minutes_late')->nullable()->after('is_late');
            $table->unsignedBigInteger('reassigned_to_team_id')->nullable()->after('minutes_late');
        });
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['is_late', 'minutes_late', 'reassigned_to_team_id']);
        });
    }
};
