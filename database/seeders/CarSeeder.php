<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Car;
use Illuminate\Support\Facades\DB;

class CarSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Car::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Car::create(['car_name' => 'Van 1']);
        Car::create(['car_name' => 'Van 2']);
        Car::create(['car_name' => 'Sedan 1']);
    }
}