<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'employee', 'external_client', 'company', 'applicant') NOT NULL DEFAULT 'external_client'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'employee', 'external_client', 'company') NOT NULL DEFAULT 'external_client'");
    }
};
