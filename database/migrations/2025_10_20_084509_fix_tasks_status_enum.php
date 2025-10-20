<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Fix the tasks table status enum to match system logic:
     * - Change 'In-Progress' to 'In Progress' (with space)
     * - Add 'On Hold' status for task delays
     *
     * @return void
     */
    public function up()
    {
        // Use raw SQL to modify the enum
        DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('Pending','Scheduled','In Progress','On Hold','Completed','Cancelled') NOT NULL DEFAULT 'Pending'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert to original enum
        DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('Pending','Scheduled','In-Progress','Completed','Cancelled') NOT NULL DEFAULT 'Pending'");
    }
};
