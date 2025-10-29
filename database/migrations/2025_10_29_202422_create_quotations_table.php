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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();

            // Step 1: Service Information
            $table->enum('booking_type', ['personal', 'company']);
            $table->json('cleaning_services')->nullable(); // Array of services for company, single for personal
            $table->date('date_of_service')->nullable(); // Only for personal
            $table->integer('duration_of_service')->nullable(); // Only for personal (in hours/days)
            $table->string('type_of_urgency')->nullable(); // Only for personal

            // Step 2: Property Information
            $table->string('property_type');
            $table->integer('floors')->default(1);
            $table->integer('rooms')->default(1);
            $table->integer('people_per_room')->nullable();
            $table->decimal('floor_area', 10, 2)->nullable();
            $table->string('area_unit')->nullable(); // 'm2' or 'sqft'

            // Property Location
            $table->string('location_type')->nullable(); // 'current' or 'select'
            $table->string('street_address')->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Step 3: Contact Information
            $table->string('company_name')->nullable(); // Only for company
            $table->string('client_name');
            $table->string('phone_number');
            $table->string('email');

            // Pricing (to be calculated by admin)
            $table->decimal('estimated_price', 10, 2)->nullable();
            $table->decimal('vat_amount', 10, 2)->nullable();
            $table->decimal('total_price', 10, 2)->nullable();
            $table->text('pricing_notes')->nullable();

            // Status and Workflow
            $table->enum('status', [
                'pending_review',     // Just submitted, waiting for admin review
                'under_review',       // Admin is reviewing
                'quoted',             // Price quote sent to client
                'accepted',           // Client accepted the quote
                'rejected',           // Admin rejected or client declined
                'converted'           // Converted to appointment
            ])->default('pending_review');

            // Admin Actions
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('quoted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('quoted_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->text('rejection_reason')->nullable();

            // Conversion to Appointment
            $table->foreignId('appointment_id')->nullable()->constrained('client_appointments')->onDelete('set null');
            $table->foreignId('converted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('converted_at')->nullable();

            // Client Response
            $table->timestamp('client_responded_at')->nullable();
            $table->text('client_message')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes for better query performance
            $table->index('booking_type');
            $table->index('status');
            $table->index('email');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quotations');
    }
};
