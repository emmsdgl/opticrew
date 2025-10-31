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
        Schema::table('contracted_clients', function (Blueprint $table) {
            $table->string('email')->nullable()->after('name');
            $table->string('phone')->nullable()->after('email');
            $table->string('address')->nullable()->after('phone');
            $table->string('business_id')->nullable()->after('address');
            $table->date('contract_start')->nullable()->after('business_id');
            $table->date('contract_end')->nullable()->after('contract_start');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contracted_clients', function (Blueprint $table) {
            $table->dropColumn(['email', 'phone', 'address', 'business_id', 'contract_start', 'contract_end']);
        });
    }
};
