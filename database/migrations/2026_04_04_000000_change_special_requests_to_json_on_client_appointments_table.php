<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Convert existing text values to JSON arrays
        DB::table('client_appointments')
            ->whereNotNull('special_requests')
            ->where('special_requests', '!=', '')
            ->orderBy('id')
            ->each(function ($row) {
                DB::table('client_appointments')
                    ->where('id', $row->id)
                    ->update([
                        'special_requests' => json_encode([$row->special_requests]),
                    ]);
            });

        Schema::table('client_appointments', function (Blueprint $table) {
            $table->json('special_requests')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Convert JSON arrays back to plain text
        DB::table('client_appointments')
            ->whereNotNull('special_requests')
            ->where('special_requests', '!=', '')
            ->orderBy('id')
            ->each(function ($row) {
                $decoded = json_decode($row->special_requests, true);
                $text = is_array($decoded) ? implode(', ', $decoded) : $row->special_requests;
                DB::table('client_appointments')
                    ->where('id', $row->id)
                    ->update(['special_requests' => $text]);
            });

        Schema::table('client_appointments', function (Blueprint $table) {
            $table->text('special_requests')->nullable()->change();
        });
    }
};
