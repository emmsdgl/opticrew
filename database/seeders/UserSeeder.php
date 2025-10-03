<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear only the admin users before creating a new one
        User::where('role', 'admin')->delete();

        User::create([
            'name' => 'Admin',
            'email' => 'admin@opticrew.com',
            'password' => Hash::make('password'), // The password will be 'password'
            'role' => 'admin'
        ]);
    }
}