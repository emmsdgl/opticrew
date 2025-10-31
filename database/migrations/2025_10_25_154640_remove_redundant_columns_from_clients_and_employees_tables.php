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
        // Remove redundant email and phone_number from clients table
        // (These already exist in the users table via user_id foreign key)
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['email', 'phone_number']);
        });

        // Remove redundant full_name from employees table
        // (This already exists as 'name' in the users table via user_id foreign key)
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('full_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Restore email and phone_number columns to clients table
        Schema::table('clients', function (Blueprint $table) {
            $table->string('email')->nullable();
            $table->string('phone_number', 20)->nullable();
        });

        // Restore full_name column to employees table
        Schema::table('employees', function (Blueprint $table) {
            $table->string('full_name')->after('user_id');
        });
    }
};
