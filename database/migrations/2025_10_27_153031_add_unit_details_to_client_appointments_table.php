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
            $table->json('unit_details')->nullable()->after('unit_size');
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
            $table->dropColumn('unit_details');
        });
    }
};
