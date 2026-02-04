<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('day_offs', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('type');
            $table->unsignedBigInteger('approved_by')->nullable()->after('status');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('admin_notes')->nullable()->after('approved_at');
            $table->date('end_date')->nullable()->after('date'); // For multi-day leave requests

            // Foreign key for approved_by (references users table)
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');

            // Index for faster status queries
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('day_offs', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropIndex(['status']);
            $table->dropColumn(['status', 'approved_by', 'approved_at', 'admin_notes', 'end_date']);
        });
    }
};
