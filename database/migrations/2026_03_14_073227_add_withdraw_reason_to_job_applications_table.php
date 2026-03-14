<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->string('withdraw_reason')->nullable()->after('status_history');
            $table->text('withdraw_details')->nullable()->after('withdraw_reason');
        });
    }

    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn(['withdraw_reason', 'withdraw_details']);
        });
    }
};
