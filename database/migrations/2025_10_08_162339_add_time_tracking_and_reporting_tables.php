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
        // 1. Create the new attendance table to log clock-ins and clock-outs
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->timestamp('clock_in')->nullable();
            $table->timestamp('clock_out')->nullable();
            $table->integer('total_minutes_worked')->nullable();
            $table->timestamps();
        });

        // 2. Create a table to store generated payroll reports
        Schema::create('payroll_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->date('pay_period_start');
            $table->date('pay_period_end');
            $table->decimal('total_hours', 8, 2);
            $table->decimal('total_pay', 10, 2);
            // Add other fields you might need, like deductions, bonuses, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
