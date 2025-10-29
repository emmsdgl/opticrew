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
        Schema::table('client_appointments', function (Blueprint $table) {
            $table->boolean('is_company_inquiry')->default(false)->after('client_id');
            $table->json('company_service_types')->nullable()->after('service_type');
            $table->text('other_concerns')->nullable()->after('special_requests');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('client_appointments', function (Blueprint $table) {
            $table->dropColumn(['is_company_inquiry', 'company_service_types', 'other_concerns']);
        });
    }
};
