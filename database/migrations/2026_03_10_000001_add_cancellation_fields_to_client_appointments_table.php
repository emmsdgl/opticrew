<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_appointments', function (Blueprint $table) {
            $table->string('cancellation_type')->nullable()->after('status'); // standard, late_cancellation, request_cancellation
            $table->decimal('cancellation_fee', 10, 2)->nullable()->after('cancellation_type');
            $table->timestamp('cancelled_at')->nullable()->after('cancellation_fee');
            $table->foreignId('cancelled_by')->nullable()->after('cancelled_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('client_appointments', function (Blueprint $table) {
            $table->dropForeign(['cancelled_by']);
            $table->dropColumn(['cancellation_type', 'cancellation_fee', 'cancelled_at', 'cancelled_by']);
        });
    }
};
