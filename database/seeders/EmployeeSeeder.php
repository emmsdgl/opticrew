<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; // Import the Str class for generating slugs

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Disable foreign key checks to safely truncate tables
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Find and delete only the user accounts that belong to employees
        $employeeUserIds = Employee::pluck('user_id');
        User::whereIn('id', $employeeUserIds)->delete();
        
        // Truncate the employees table
        Employee::truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $csvFile = fopen(base_path("database/seeders/data/employees.csv"), "r");

        // Skip the header row
        fgetcsv($csvFile); 

        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
            if (empty($data[1])) continue; // Skip empty rows

            // --- THIS IS THE KEY CHANGE ---
            // Create a simple, predictable email address
            $email = Str::slug($data[1], '') . '@finnoys.com';

            // Create a user account for the employee
            $user = User::create([
                'name' => $data[1], // Employee Name
                'email' => $email,
                'password' => Hash::make('password'), // All employees have the same password for easy testing
                'role' => 'employee',
            ]);

            // Create the employee profile linked to the user account
            Employee::create([
                'user_id' => $user->id,
                'full_name' => $data[1],
                'skills' => json_encode(explode(' & ', $data[2])),
            ]);
        }

        fclose($csvFile);
    }
}