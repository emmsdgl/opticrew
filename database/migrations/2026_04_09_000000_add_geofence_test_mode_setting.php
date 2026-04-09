<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $exists = DB::table('company_settings')->where('key', 'geofence_test_mode')->exists();

        if (!$exists) {
            DB::table('company_settings')->insert([
                'key' => 'geofence_test_mode',
                'value' => 'PH',
                'type' => 'string',
                'description' => 'Geofence demo mode: PH bypasses the geofence (for local testing/demo); FN enforces the real Finland radius.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down()
    {
        DB::table('company_settings')->where('key', 'geofence_test_mode')->delete();
    }
};