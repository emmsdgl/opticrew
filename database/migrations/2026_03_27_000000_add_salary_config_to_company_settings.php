<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $settings = [
            [
                'key' => 'salary_full_time',
                'value' => '2500',
                'type' => 'decimal',
                'description' => 'Default base salary for full-time job postings (EUR)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'salary_part_time',
                'value' => '1200',
                'type' => 'decimal',
                'description' => 'Default base salary for part-time job postings (EUR)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'salary_remote',
                'value' => '2000',
                'type' => 'decimal',
                'description' => 'Default base salary for remote job postings (EUR)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('company_settings')->updateOrInsert(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    public function down(): void
    {
        DB::table('company_settings')->whereIn('key', [
            'salary_full_time',
            'salary_part_time',
            'salary_remote',
        ])->delete();
    }
};
