<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('client_appointments', function (Blueprint $table) {
            $table->boolean('is_urgent')->default(false)->after('is_holiday');
            $table->boolean('premium_surge_accepted')->default(false)->after('is_urgent');
            $table->decimal('surge_multiplier', 4, 2)->nullable()->after('premium_surge_accepted');
        });
    }

    public function down(): void
    {
        Schema::table('client_appointments', function (Blueprint $table) {
            $table->dropColumn(['is_urgent', 'premium_surge_accepted', 'surge_multiplier']);
        });
    }
};
