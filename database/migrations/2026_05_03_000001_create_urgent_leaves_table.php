<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('urgent_leaves', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('attendance_id')->nullable();
            $table->timestamp('triggered_at');
            $table->timestamp('clock_out_at')->nullable();
            $table->text('reason')->nullable();
            $table->enum('status', [
                'awaiting_admin',     // employee submitted; admin grace period running
                'auto_assigned',      // grace expired, system auto-assigned a replacement
                'manually_assigned',  // admin picked the replacement and set compensation
                'cancelled',          // admin cancelled (e.g., false alarm)
            ])->default('awaiting_admin');
            $table->unsignedBigInteger('replacement_employee_id')->nullable();
            $table->decimal('compensation_amount', 10, 2)->nullable();
            $table->unsignedTinyInteger('escalation_level')->default(0);
            $table->timestamp('auto_escalation_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
            $table->foreign('attendance_id')->references('id')->on('attendances')->nullOnDelete();
            $table->foreign('replacement_employee_id')->references('id')->on('employees')->nullOnDelete();
            $table->foreign('processed_by')->references('id')->on('users')->nullOnDelete();

            $table->index(['status', 'triggered_at']);
            $table->index('employee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('urgent_leaves');
    }
};
