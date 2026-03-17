<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProductionSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $tables = [
            'checklist_items',
            'checklist_categories',
            'company_checklists',
            'company_settings',
            'cars',
            'locations',
            'contracted_clients',
            'employees',
            'users',
            'job_postings',
        ];

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }

        $now = now();
        $hashedPassword = Hash::make('password');

        // 1. Users
        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'Admin',
                'username' => null,
                'email' => 'admin@opticrew.com',
                'alternative_email' => null,
                'google_id' => null,
                'profile_picture' => null,
                'phone' => null,
                'location' => null,
                'email_verified_at' => $now,
                'password' => $hashedPassword,
                'role' => 'admin',
                'terms_accepted_at' => null,
                'remember_token' => null,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
        ]);

        $employees = [
            [2, 'Vincent Rey Digol', 'vincentreydigol@finnoys.com'],
            [3, 'Martin Yvann Leonardo', 'martinyvannleonardo@finnoys.com'],
            [4, 'Earl Leonardo', 'earlleonardo@finnoys.com'],
            [5, 'Merlyn Guzman', 'merlynguzman@finnoys.com'],
            [6, 'Aries Guzman', 'ariesguzman@finnoys.com'],
            [7, 'Bella Ostan', 'bellaostan@finnoys.com'],
            [8, 'Jennylyn Saballero', 'jennylynsaballero@finnoys.com'],
            [9, 'Rizza Estrella', 'rizzaestrella@finnoys.com'],
            [10, 'Cherrylyn Morales', 'cherrylynmorales@finnoys.com'],
            [11, 'John Carl Morales', 'johncarlmorales@finnoys.com'],
            [12, 'John Kevin Morales', 'johnkevinmorales@finnoys.com'],
        ];

        foreach ($employees as $emp) {
            DB::table('users')->insert([
                'id' => $emp[0],
                'name' => $emp[1],
                'username' => null,
                'email' => $emp[2],
                'alternative_email' => null,
                'google_id' => null,
                'profile_picture' => null,
                'phone' => null,
                'location' => null,
                'email_verified_at' => $now,
                'password' => $hashedPassword,
                'role' => 'employee',
                'terms_accepted_at' => null,
                'remember_token' => null,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ]);
        }

        $companies = [
            [19, 'Kakslauttanen', 'kakslauttanen@company.com'],
            [20, 'Aikamatkat', 'aikamatkat@company.com'],
        ];

        foreach ($companies as $comp) {
            DB::table('users')->insert([
                'id' => $comp[0],
                'name' => $comp[1],
                'username' => null,
                'email' => $comp[2],
                'alternative_email' => null,
                'google_id' => null,
                'profile_picture' => null,
                'phone' => null,
                'location' => null,
                'email_verified_at' => $now,
                'password' => $hashedPassword,
                'role' => 'company',
                'terms_accepted_at' => null,
                'remember_token' => null,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ]);
        }

        // 2. Employees
        $employeeRecords = [
            [1, 2, '["Driving","Cleaning"]', 1],
            [2, 3, '["Driving","Cleaning"]', 1],
            [3, 4, '["Driving","Cleaning"]', 1],
            [4, 5, '["Driving","Cleaning"]', 1],
            [5, 6, '["Driving","Cleaning"]', 1],
            [6, 7, '["Cleaning"]', 0],
            [7, 8, '["Cleaning"]', 0],
            [8, 9, '["Cleaning"]', 0],
            [9, 10, '["Driving","Cleaning"]', 1],
            [10, 11, '["Driving","Cleaning"]', 1],
            [11, 12, '["Driving","Cleaning"]', 1],
        ];

        foreach ($employeeRecords as $rec) {
            DB::table('employees')->insert([
                'id' => $rec[0],
                'user_id' => $rec[1],
                'skills' => $rec[2],
                'is_active' => 1,
                'is_day_off' => 0,
                'is_busy' => 0,
                'efficiency' => 1.00,
                'has_driving_license' => $rec[3],
                'years_of_experience' => 0,
                'salary_per_hour' => 13.00,
                'created_at' => $now,
                'updated_at' => $now,
                'months_employed' => 0,
                'deleted_at' => null,
            ]);
        }

        // 3. Contracted Clients
        DB::table('contracted_clients')->insert([
            [
                'id' => 1,
                'user_id' => 19,
                'name' => 'Kakslauttanen',
                'email' => 'kakslauttanen@company.com',
                'phone' => '+358 00 000 0000',
                'address' => 'Address to be updated',
                'business_id' => '1234567-8',
                'contract_start' => null,
                'contract_end' => null,
                'latitude' => 68.33470361,
                'longitude' => 27.33426652,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'id' => 2,
                'user_id' => 20,
                'name' => 'Aikamatkat',
                'email' => 'aikamatkat@company.com',
                'phone' => '+358 00 000 0000',
                'address' => 'Address to be updated',
                'business_id' => '1234567-0',
                'contract_start' => null,
                'contract_end' => null,
                'latitude' => 14.52682705,
                'longitude' => 121.01600925,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
        ]);

        // 4. Locations
        // Kakslauttanen locations (contracted_client_id=1)
        for ($i = 1; $i <= 12; $i++) {
            DB::table('locations')->insert([
                'contracted_client_id' => 1,
                'location_name' => "Small Cabin #$i",
                'location_type' => 'Small Cabin',
                'base_cleaning_duration_minutes' => 60,
                'normal_rate_per_hour' => 42.00,
                'sunday_holiday_rate' => 84.00,
                'deep_cleaning_rate' => null,
                'light_deep_cleaning_rate' => null,
                'student_rate' => 2.00,
                'student_sunday_holiday_rate' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ]);
        }

        for ($i = 1; $i <= 6; $i++) {
            DB::table('locations')->insert([
                'contracted_client_id' => 1,
                'location_name' => "Medium Cabin #$i",
                'location_type' => 'Medium Cabin',
                'base_cleaning_duration_minutes' => 60,
                'normal_rate_per_hour' => 51.00,
                'sunday_holiday_rate' => 102.00,
                'deep_cleaning_rate' => null,
                'light_deep_cleaning_rate' => null,
                'student_rate' => null,
                'student_sunday_holiday_rate' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ]);
        }

        for ($i = 1; $i <= 13; $i++) {
            DB::table('locations')->insert([
                'contracted_client_id' => 1,
                'location_name' => "Big Cabin #$i",
                'location_type' => 'Big Cabin',
                'base_cleaning_duration_minutes' => 60,
                'normal_rate_per_hour' => 60.00,
                'sunday_holiday_rate' => 120.00,
                'deep_cleaning_rate' => null,
                'light_deep_cleaning_rate' => null,
                'student_rate' => null,
                'student_sunday_holiday_rate' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ]);
        }

        for ($i = 1; $i <= 5; $i++) {
            DB::table('locations')->insert([
                'contracted_client_id' => 1,
                'location_name' => "Queen Suite #$i",
                'location_type' => 'Queen Suite',
                'base_cleaning_duration_minutes' => 60,
                'normal_rate_per_hour' => 60.00,
                'sunday_holiday_rate' => 120.00,
                'deep_cleaning_rate' => null,
                'light_deep_cleaning_rate' => null,
                'student_rate' => null,
                'student_sunday_holiday_rate' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ]);
        }

        for ($i = 1; $i <= 20; $i++) {
            DB::table('locations')->insert([
                'contracted_client_id' => 1,
                'location_name' => "Igloo #$i",
                'location_type' => 'Igloo',
                'base_cleaning_duration_minutes' => 45,
                'normal_rate_per_hour' => 30.00,
                'sunday_holiday_rate' => 60.00,
                'deep_cleaning_rate' => null,
                'light_deep_cleaning_rate' => null,
                'student_rate' => null,
                'student_sunday_holiday_rate' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ]);
        }

        DB::table('locations')->insert([
            'contracted_client_id' => 1,
            'location_name' => 'Traditional House',
            'location_type' => 'Traditional House',
            'base_cleaning_duration_minutes' => 60,
            'normal_rate_per_hour' => 60.00,
            'sunday_holiday_rate' => 120.00,
            'deep_cleaning_rate' => null,
            'light_deep_cleaning_rate' => null,
            'student_rate' => null,
            'student_sunday_holiday_rate' => null,
            'created_at' => $now,
            'updated_at' => $now,
            'deleted_at' => null,
        ]);

        DB::table('locations')->insert([
            'contracted_client_id' => 1,
            'location_name' => 'Turf Chamber',
            'location_type' => 'Turf Chamber',
            'base_cleaning_duration_minutes' => 60,
            'normal_rate_per_hour' => 60.00,
            'sunday_holiday_rate' => 120.00,
            'deep_cleaning_rate' => null,
            'light_deep_cleaning_rate' => null,
            'student_rate' => null,
            'student_sunday_holiday_rate' => null,
            'created_at' => $now,
            'updated_at' => $now,
            'deleted_at' => null,
        ]);

        // Aikamatkat locations (contracted_client_id=2)
        for ($i = 1; $i <= 12; $i++) {
            DB::table('locations')->insert([
                'contracted_client_id' => 2,
                'location_name' => "Panimo Cabins #$i",
                'location_type' => 'Panimo Cabins',
                'base_cleaning_duration_minutes' => 60,
                'normal_rate_per_hour' => 68.25,
                'sunday_holiday_rate' => 120.50,
                'deep_cleaning_rate' => 210.00,
                'light_deep_cleaning_rate' => 110.00,
                'student_rate' => 36.75,
                'student_sunday_holiday_rate' => 55.50,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ]);
        }

        DB::table('locations')->insert([
            [
                'contracted_client_id' => 2,
                'location_name' => 'Metsakoti A',
                'location_type' => 'Metsakoti A',
                'base_cleaning_duration_minutes' => 60,
                'normal_rate_per_hour' => 84.00,
                'sunday_holiday_rate' => 126.00,
                'deep_cleaning_rate' => 210.00,
                'light_deep_cleaning_rate' => 110.00,
                'student_rate' => 36.75,
                'student_sunday_holiday_rate' => 55.50,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'contracted_client_id' => 2,
                'location_name' => 'Metsakoti B',
                'location_type' => 'Metsakoti B',
                'base_cleaning_duration_minutes' => 60,
                'normal_rate_per_hour' => 84.00,
                'sunday_holiday_rate' => 126.00,
                'deep_cleaning_rate' => 210.00,
                'light_deep_cleaning_rate' => 110.00,
                'student_rate' => 47.25,
                'student_sunday_holiday_rate' => 70.90,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'contracted_client_id' => 2,
                'location_name' => 'Kermikkas',
                'location_type' => 'Kermikkas',
                'base_cleaning_duration_minutes' => 60,
                'normal_rate_per_hour' => 36.75,
                'sunday_holiday_rate' => 55.50,
                'deep_cleaning_rate' => null,
                'light_deep_cleaning_rate' => null,
                'student_rate' => 20.00,
                'student_sunday_holiday_rate' => 30.00,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'contracted_client_id' => 2,
                'location_name' => 'Hirvasaho A2 and B1',
                'location_type' => 'Hirvasaho A2 and B1',
                'base_cleaning_duration_minutes' => 60,
                'normal_rate_per_hour' => 36.75,
                'sunday_holiday_rate' => 55.50,
                'deep_cleaning_rate' => null,
                'light_deep_cleaning_rate' => null,
                'student_rate' => 20.00,
                'student_sunday_holiday_rate' => 30.00,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'contracted_client_id' => 2,
                'location_name' => 'Hirvasaho B2',
                'location_type' => 'Hirvasaho B2',
                'base_cleaning_duration_minutes' => 60,
                'normal_rate_per_hour' => 68.25,
                'sunday_holiday_rate' => 102.50,
                'deep_cleaning_rate' => null,
                'light_deep_cleaning_rate' => null,
                'student_rate' => 36.75,
                'student_sunday_holiday_rate' => 55.50,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'contracted_client_id' => 2,
                'location_name' => 'Hirvas Apartments',
                'location_type' => 'Hirvas Apartments',
                'base_cleaning_duration_minutes' => 60,
                'normal_rate_per_hour' => 36.75,
                'sunday_holiday_rate' => 55.50,
                'deep_cleaning_rate' => null,
                'light_deep_cleaning_rate' => null,
                'student_rate' => 20.00,
                'student_sunday_holiday_rate' => 30.00,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'contracted_client_id' => 2,
                'location_name' => 'Voursa 3A and 3B',
                'location_type' => 'Voursa 3A and 3B',
                'base_cleaning_duration_minutes' => 60,
                'normal_rate_per_hour' => 36.75,
                'sunday_holiday_rate' => 55.50,
                'deep_cleaning_rate' => null,
                'light_deep_cleaning_rate' => null,
                'student_rate' => 20.00,
                'student_sunday_holiday_rate' => 30.00,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'contracted_client_id' => 2,
                'location_name' => 'Voursa 3C',
                'location_type' => 'Voursa 3C',
                'base_cleaning_duration_minutes' => 60,
                'normal_rate_per_hour' => 68.25,
                'sunday_holiday_rate' => 102.50,
                'deep_cleaning_rate' => null,
                'light_deep_cleaning_rate' => null,
                'student_rate' => 36.75,
                'student_sunday_holiday_rate' => 55.50,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'contracted_client_id' => 2,
                'location_name' => 'Moitakuru C31 and C32',
                'location_type' => 'Moitakuru C31 and C32',
                'base_cleaning_duration_minutes' => 60,
                'normal_rate_per_hour' => 57.75,
                'sunday_holiday_rate' => 87.50,
                'deep_cleaning_rate' => null,
                'light_deep_cleaning_rate' => null,
                'student_rate' => 31.50,
                'student_sunday_holiday_rate' => 47.25,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'contracted_client_id' => 2,
                'location_name' => 'Luulampi',
                'location_type' => 'Luulampi',
                'base_cleaning_duration_minutes' => 60,
                'normal_rate_per_hour' => 68.25,
                'sunday_holiday_rate' => 102.50,
                'deep_cleaning_rate' => null,
                'light_deep_cleaning_rate' => null,
                'student_rate' => 36.75,
                'student_sunday_holiday_rate' => 55.50,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'contracted_client_id' => 2,
                'location_name' => 'Metashirvas',
                'location_type' => 'Metashirvas',
                'base_cleaning_duration_minutes' => 60,
                'normal_rate_per_hour' => 73.50,
                'sunday_holiday_rate' => 110.25,
                'deep_cleaning_rate' => null,
                'light_deep_cleaning_rate' => null,
                'student_rate' => 39.75,
                'student_sunday_holiday_rate' => 59.50,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'contracted_client_id' => 2,
                'location_name' => 'Kelotähti',
                'location_type' => 'Kelotähti',
                'base_cleaning_duration_minutes' => 60,
                'normal_rate_per_hour' => 73.50,
                'sunday_holiday_rate' => 110.25,
                'deep_cleaning_rate' => null,
                'light_deep_cleaning_rate' => null,
                'student_rate' => 39.75,
                'student_sunday_holiday_rate' => 59.50,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
            [
                'contracted_client_id' => 2,
                'location_name' => 'Raahenmaja',
                'location_type' => 'Raahenmaja',
                'base_cleaning_duration_minutes' => 60,
                'normal_rate_per_hour' => 94.50,
                'sunday_holiday_rate' => 141.75,
                'deep_cleaning_rate' => null,
                'light_deep_cleaning_rate' => null,
                'student_rate' => 51.00,
                'student_sunday_holiday_rate' => 76.50,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
        ]);

        // 5. Cars
        DB::table('cars')->insert([
            ['car_name' => 'Van 1', 'is_available' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['car_name' => 'Van 2', 'is_available' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['car_name' => 'Sedan 1', 'is_available' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 6. Company Settings
        DB::table('company_settings')->insert([
            ['key' => 'office_latitude', 'value' => '0', 'type' => 'decimal', 'description' => null, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'office_longitude', 'value' => '0', 'type' => 'decimal', 'description' => null, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'geofence_radius', 'value' => '110', 'type' => 'integer', 'description' => null, 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'reassignment_grace_period_minutes', 'value' => '30', 'type' => 'integer', 'description' => 'Scenario #18: Grace period (minutes) for task reassignment after leave approval', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'task_approval_grace_period_minutes', 'value' => '30', 'type' => 'integer', 'description' => 'Scenario #19: Grace period (minutes) for employee to approve/start assigned task', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'unstaffed_escalation_timeout_minutes', 'value' => '60', 'type' => 'integer', 'description' => 'Scenario #15: Minutes before CRITICAL_ESCALATION for unaccepted tasks', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'overtime_threshold_hours', 'value' => '8', 'type' => 'integer', 'description' => 'Scenario #16: Hours after which overtime pay is computed', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'minimum_booking_notice_days', 'value' => '3', 'type' => 'integer', 'description' => 'Scenario #1: Minimum days notice required for booking', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'minimum_leave_notice_days', 'value' => '4', 'type' => 'integer', 'description' => 'Scenario #13: Minimum days notice for standard leave requests', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 7. Company Checklists
        DB::table('company_checklists')->insert([
            'id' => 1,
            'contracted_client_id' => 2,
            'name' => 'Aikamatkat Checklist',
            'important_reminders' => 'this is important',
            'is_active' => 1,
        ]);

        // 8. Checklist Categories
        DB::table('checklist_categories')->insert([
            ['id' => 1, 'checklist_id' => 1, 'name' => 'Kitchen', 'sort_order' => 0],
            ['id' => 2, 'checklist_id' => 1, 'name' => 'Bathroom', 'sort_order' => 1],
        ]);

        // 9. Checklist Items
        DB::table('checklist_items')->insert([
            ['id' => 1, 'category_id' => 1, 'name' => 'clean', 'quantity' => 3, 'sort_order' => 0],
            ['id' => 2, 'category_id' => 2, 'name' => 'tub clean', 'quantity' => 3, 'sort_order' => 0],
        ]);

        // 10. Job Postings
        DB::table('job_postings')->insert([
            'id' => 1,
            'title' => 'Deep Cleaning Job',
            'description' => 'House Deep cleaning is different from regular or spring cleaning because it reaches the deep grime and dirt in your home.',
            'location' => 'Inari, Finland',
            'salary' => 13,
            'type' => 'full-time',
            'type_badge' => 'Full-time Employee',
            'icon' => 'fa-broom',
            'icon_color' => 'blue',
            'is_active' => 1,
            'status' => 'published',
            'required_skills' => json_encode(['Surface Sanitization', 'Disinfection Procedures', 'Waste Disposal', 'Deep Cleaning', 'Carpet Cleaning', 'Restroom Sanitation']),
            'required_docs' => json_encode([
                ['name' => 'Resume', 'fileType' => 'docx,pdf'],
                ['name' => 'Cover Letter', 'fileType' => 'docx,pdf'],
            ]),
            'benefits' => json_encode(['Health Insurance', 'Paid Leave', 'Transportation Allowance']),
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
