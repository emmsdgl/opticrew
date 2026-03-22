<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotation_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed defaults
        DB::table('quotation_settings')->insert([
            ['key' => 'auto_send_enabled', 'value' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'pdf_deep_cleaning', 'value' => null, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'pdf_final_cleaning', 'value' => null, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'pdf_daily_cleaning', 'value' => null, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'pdf_snowout_cleaning', 'value' => null, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'pdf_general_cleaning', 'value' => null, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'pdf_hotel_cleaning', 'value' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_settings');
    }
};
