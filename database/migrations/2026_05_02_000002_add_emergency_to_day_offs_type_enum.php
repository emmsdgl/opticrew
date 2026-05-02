<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE day_offs MODIFY type ENUM('vacation','sick','personal','other','emergency') NOT NULL DEFAULT 'personal'");
    }

    public function down(): void
    {
        // Convert any rows that used 'emergency' back to 'sick' before removing the value
        DB::table('day_offs')->where('type', 'emergency')->update(['type' => 'sick']);
        DB::statement("ALTER TABLE day_offs MODIFY type ENUM('vacation','sick','personal','other') NOT NULL DEFAULT 'personal'");
    }
};
