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
        Schema::create('client_appointments', function (Blueprint $table) {
            $table->id();

            // Client Information
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->string('booking_type'); // 'personal' or 'company'

            // Service Details
            $table->string('service_type');
            $table->date('service_date');
            $table->time('service_time');
            $table->boolean('is_sunday')->default(false);
            $table->boolean('is_holiday')->default(false);

            // Unit Details
            $table->integer('number_of_units');
            $table->string('unit_size'); // '40-60', '60-90', etc.
            $table->string('cabin_name'); // Room identifier
            $table->text('special_requests')->nullable();

            // Pricing
            $table->decimal('quotation', 10, 2); // Price excluding VAT
            $table->decimal('vat_amount', 10, 2); // VAT (24%)
            $table->decimal('total_amount', 10, 2); // Total including VAT

            // Status & Assignment
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed', 'cancelled'])->default('pending');
            $table->foreignId('assigned_team_id')->nullable()->constrained('optimization_teams')->onDelete('set null');
            $table->foreignId('recommended_team_id')->nullable()->constrained('optimization_teams')->onDelete('set null');

            // Admin Actions
            $table->text('rejection_reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('rejected_at')->nullable();

            // Notifications
            $table->boolean('client_notified')->default(false);
            $table->timestamp('notified_at')->nullable();

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
        Schema::dropIfExists('client_appointments');
    }
};
