<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CarSeeder::class,               // <-- ADD THIS LINE
            UserSeeder::class,              // Creates the admin user
            EmployeeSeeder::class,          // Creates employees and their user accounts
            ContractedClientSeeder::class,  // Creates Kakslauttanen & Aikamatkat
            LocationSeeder::class,          // Creates all the individual cabins (MUST run after clients)
        ]);
    }
}

?>