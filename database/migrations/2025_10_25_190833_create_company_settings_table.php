<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, integer, decimal, boolean, json
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default geofencing settings (Philippines coordinates for testing)
        DB::table('company_settings')->insert([
            [
                'key' => 'office_latitude',
                'value' => '14.5995',  // Manila, Philippines (Makati CBD) for testing
                'type' => 'decimal',
                'description' => 'Office location latitude coordinate',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'office_longitude',
                'value' => '120.9842',  // Manila, Philippines (Makati CBD) for testing
                'type' => 'decimal',
                'description' => 'Office location longitude coordinate',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'geofence_radius',
                'value' => '100',  // 100 meters
                'type' => 'integer',
                'description' => 'Geofence radius in meters for clock in/out',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_settings');
    }
};
