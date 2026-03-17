<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE job_applications MODIFY COLUMN status ENUM('pending','reviewed','interview_scheduled','hired','rejected','withdrawn') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE job_applications MODIFY COLUMN status ENUM('pending','reviewed','interview_scheduled','hired','rejected') NOT NULL DEFAULT 'pending'");
    }
};
