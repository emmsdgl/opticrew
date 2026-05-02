<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('employee_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('day_off_id')->nullable()->after('id');
            $table->foreign('day_off_id')->references('id')->on('day_offs')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('employee_requests', function (Blueprint $table) {
            $table->dropForeign(['day_off_id']);
            $table->dropColumn('day_off_id');
        });
    }
};
