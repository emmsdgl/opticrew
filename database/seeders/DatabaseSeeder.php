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
            ProductionSeeder::class,        // Seeds admin, employees, locations, settings, etc.
            TrainingVideoSeeder::class,     // Creates training videos for employee app
        ]);
    }
}

?>