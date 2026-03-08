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
            $table->string('state', 100)->nullable()->after('street_address');
            $table->dropColumn(['postal_code', 'district']);
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
            $table->string('postal_code', 5)->nullable()->after('street_address');
            $table->string('district', 100)->nullable()->after('city');
            $table->dropColumn('state');
        });
    }
};
