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
        Schema::table('clients', function (Blueprint $table) {
            // Add client_type if it doesn't exist (from original schema)
            if (!Schema::hasColumn('clients', 'client_type')) {
                $table->enum('client_type', ['personal', 'company'])->nullable();
            }

            // Add all Finnish address fields without 'after' positioning
            if (!Schema::hasColumn('clients', 'company_name')) {
                $table->string('company_name')->nullable();
            }
            if (!Schema::hasColumn('clients', 'first_name')) {
                $table->string('first_name')->nullable();
            }
            if (!Schema::hasColumn('clients', 'last_name')) {
                $table->string('last_name')->nullable();
            }
            if (!Schema::hasColumn('clients', 'middle_initial')) {
                $table->string('middle_initial', 10)->nullable();
            }
            if (!Schema::hasColumn('clients', 'birthdate')) {
                $table->date('birthdate')->nullable();
            }
            if (!Schema::hasColumn('clients', 'email')) {
                $table->string('email')->nullable();
            }
            if (!Schema::hasColumn('clients', 'phone_number')) {
                $table->string('phone_number', 20)->nullable();
            }
            if (!Schema::hasColumn('clients', 'street_address')) {
                $table->string('street_address')->nullable();
            }
            if (!Schema::hasColumn('clients', 'postal_code')) {
                $table->string('postal_code', 10)->nullable();
            }
            if (!Schema::hasColumn('clients', 'city')) {
                $table->string('city', 100)->nullable();
            }
            if (!Schema::hasColumn('clients', 'district')) {
                $table->string('district', 100)->nullable();
            }
            if (!Schema::hasColumn('clients', 'address')) {
                $table->text('address')->nullable();
            }
            if (!Schema::hasColumn('clients', 'billing_address')) {
                $table->text('billing_address')->nullable();
            }
            if (!Schema::hasColumn('clients', 'einvoice_number')) {
                $table->string('einvoice_number', 100)->nullable();
            }
            if (!Schema::hasColumn('clients', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
            if (!Schema::hasColumn('clients', 'security_question_1')) {
                $table->string('security_question_1')->nullable();
            }
            if (!Schema::hasColumn('clients', 'security_answer_1')) {
                $table->string('security_answer_1')->nullable();
            }
            if (!Schema::hasColumn('clients', 'security_question_2')) {
                $table->string('security_question_2')->nullable();
            }
            if (!Schema::hasColumn('clients', 'security_answer_2')) {
                $table->string('security_answer_2')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'middle_initial',
                'birthdate',
                'email',
                'phone_number',
                'street_address',
                'postal_code',
                'city',
                'district',
                'address',
                'billing_address',
                'einvoice_number',
                'is_active',
                'security_question_1',
                'security_answer_1',
                'security_question_2',
                'security_answer_2'
            ]);
        });
    }
};
