<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('attendances', 'status')) {
                $table->string('status')->nullable()->after('total_minutes_worked');
            }
            if (!Schema::hasColumn('attendances', 'hours_worked')) {
                $table->decimal('hours_worked', 8, 2)->nullable()->after('total_minutes_worked');
            }
            if (!Schema::hasColumn('attendances', 'clock_in_photo')) {
                $table->string('clock_in_photo')->nullable()->after('clock_out_longitude');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['status', 'hours_worked', 'clock_in_photo']);
        });
    }
};
