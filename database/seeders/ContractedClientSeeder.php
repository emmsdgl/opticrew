<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContractedClient;
use Illuminate\Support\Facades\DB;

class ContractedClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    // public function run()
    // {
    //     // Truncate the table first to avoid duplicates on re-seeding
    //     DB::table('contracted_clients')->truncate();

    //     ContractedClient::create(['name' => 'Kakslauttanen']);
    //     ContractedClient::create(['name' => 'Aikamatkat']);
    // }

    public function run()
    {
        // Temporarily disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        ContractedClient::truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        ContractedClient::create(['name' => 'Kakslauttanen']);
        ContractedClient::create(['name' => 'Aikamatkat']);
    }
}

?>