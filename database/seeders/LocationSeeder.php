<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContractedClient;
use App\Models\Location;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Temporarily disable foreign key checks because the 'tasks' table depends on this one.
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        Location::truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_key_CHECKS=1;');

        // --- KAKSLAUTTANEN LOCATIONS ---
        $kakslauttanen = ContractedClient::where('name', 'Kakslauttanen')->first();
        
        // Truncate the table first
        // DB::table('locations')->truncate();

        // --- KAKSLAUTTANEN LOCATIONS ---
        $kakslauttanen = ContractedClient::where('name', 'Kakslauttanen')->first();

        if ($kakslauttanen) {
            // Create 12 Small Cabins
            for ($i = 1; $i <= 12; $i++) {
                Location::create([
                    'contracted_client_id' => $kakslauttanen->id,
                    'location_name' => "Small Cabin #{$i}",
                    'location_type' => 'Small Cabin',
                    'base_cleaning_duration_minutes' => 60,
                ]);
            }
            // Create 6 Medium Cabins
            for ($i = 1; $i <= 6; $i++) {
                Location::create([
                    'contracted_client_id' => $kakslauttanen->id,
                    'location_name' => "Medium Cabin #{$i}",
                    'location_type' => 'Medium Cabin',
                    'base_cleaning_duration_minutes' => 60,
                ]);
            }
            // Create 13 Big Cabins
            for ($i = 1; $i <= 13; $i++) {
                Location::create([
                    'contracted_client_id' => $kakslauttanen->id,
                    'location_name' => "Big Cabin #{$i}",
                    'location_type' => 'Big Cabin',
                    'base_cleaning_duration_minutes' => 60,
                ]);
            }
            // Create 5 Queen Suites
            for ($i = 1; $i <= 5; $i++) {
                Location::create([
                    'contracted_client_id' => $kakslauttanen->id,
                    'location_name' => "Queen Suite #{$i}",
                    'location_type' => 'Queen Suite',
                    'base_cleaning_duration_minutes' => 60,
                ]);
            }
             // Create 20 Igloos
             for ($i = 1; $i <= 20; $i++) {
                Location::create([
                    'contracted_client_id' => $kakslauttanen->id,
                    'location_name' => "Igloo #{$i}",
                    'location_type' => 'Igloo',
                    'base_cleaning_duration_minutes' => 45,
                ]);
            }
            // Create 1 Traditional House and 1 Turf Chamber
            Location::create(['contracted_client_id' => $kakslauttanen->id, 'location_name' => 'Traditional House', 'location_type' => 'Traditional House', 'base_cleaning_duration_minutes' => 60]);
            Location::create(['contracted_client_id' => $kakslauttanen->id, 'location_name' => 'Turf Chamber', 'location_type' => 'Turf Chamber', 'base_cleaning_duration_minutes' => 60]);
        }


        // --- AIKAMATKAT LOCATIONS ---
        $aikamatkat = ContractedClient::where('name', 'Aikamatkat')->first();
        
        if ($aikamatkat) {
            $aikamatkat_locations = [
                'Panimo Cabins' => 12,
                'Metsakoti A' => 1,
                'Metsakoti B' => 1,
                'Kermikkas' => 1, // Assuming 1 for items without a number
                'Hirvasaho A2 and B1' => 1,
                'Hirvasaho B2' => 1,
                'Hirvas Apartments' => 1,
                'Voursa 3A and 3B' => 1,
                'Voursa 3C' => 1,
                'Moitakuru C31 and C32' => 1,
                'Luulampi' => 1,
                'Metashirvas' => 1,
                'Kelotähti' => 1,
                'Raahenmaja' => 1,
            ];

            foreach ($aikamatkat_locations as $type => $count) {
                for ($i = 1; $i <= $count; $i++) {
                    Location::create([
                        'contracted_client_id' => $aikamatkat->id,
                        'location_name' => $count > 1 ? "{$type} #{$i}" : $type,
                        'location_type' => $type,
                        'base_cleaning_duration_minutes' => 60, // Default duration
                    ]);
                }
            }
        }
    }
}

?>