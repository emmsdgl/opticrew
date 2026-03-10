<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('day_offs', function (Blueprint $table) {
            $table->boolean('is_emergency')->default(false)->after('type');
            $table->tinyInteger('escalation_level')->default(0)->after('is_emergency');
            $table->timestamp('escalation_notified_at')->nullable()->after('escalation_level');
            $table->boolean('auto_escalation_locked')->default(false)->after('escalation_notified_at');
        });

        // Add scenario-related company settings
        DB::table('company_settings')->insert([
            [
                'key' => 'reassignment_grace_period_minutes',
                'value' => '30',
                'type' => 'integer',
                'description' => 'Scenario #18: Grace period (minutes) for task reassignment after leave approval',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'task_approval_grace_period_minutes',
                'value' => '30',
                'type' => 'integer',
                'description' => 'Scenario #19: Grace period (minutes) for employee to approve/start assigned task',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'unstaffed_escalation_timeout_minutes',
                'value' => '60',
                'type' => 'integer',
                'description' => 'Scenario #15: Minutes before CRITICAL_ESCALATION for unaccepted tasks',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'overtime_threshold_hours',
                'value' => '8',
                'type' => 'integer',
                'description' => 'Scenario #16: Hours after which overtime pay is computed',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'minimum_booking_notice_days',
                'value' => '3',
                'type' => 'integer',
                'description' => 'Scenario #1: Minimum days notice required for booking',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'minimum_leave_notice_days',
                'value' => '4',
                'type' => 'integer',
                'description' => 'Scenario #13: Minimum days notice for standard leave requests',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::table('day_offs', function (Blueprint $table) {
            $table->dropColumn(['is_emergency', 'escalation_level', 'escalation_notified_at', 'auto_escalation_locked']);
        });

        DB::table('company_settings')->whereIn('key', [
            'reassignment_grace_period_minutes',
            'task_approval_grace_period_minutes',
            'unstaffed_escalation_timeout_minutes',
            'overtime_threshold_hours',
            'minimum_booking_notice_days',
            'minimum_leave_notice_days',
        ])->delete();
    }
};
