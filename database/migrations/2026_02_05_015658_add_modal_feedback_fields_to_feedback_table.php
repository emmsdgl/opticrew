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
        Schema::table('feedback', function (Blueprint $table) {
            // Add reference fields (only if they don't exist)
            if (!Schema::hasColumn('feedback', 'task_id')) {
                $table->foreignId('task_id')->nullable()->constrained('tasks')->onDelete('cascade');
            }
            if (!Schema::hasColumn('feedback', 'appointment_id')) {
                $table->foreignId('appointment_id')->nullable()->constrained('client_appointments')->onDelete('cascade');
            }
            if (!Schema::hasColumn('feedback', 'employee_id')) {
                $table->foreignId('employee_id')->nullable()->constrained('employees')->onDelete('cascade');
            }

            // Add user type to distinguish between client and employee feedback
            if (!Schema::hasColumn('feedback', 'user_type')) {
                $table->enum('user_type', ['client', 'employee'])->nullable();
            }

            // Add emoji rating (1-5 scale)
            if (!Schema::hasColumn('feedback', 'rating')) {
                $table->integer('rating')->nullable()->comment('Emoji rating from 1-5');
            }

            // Add keywords as JSON array
            if (!Schema::hasColumn('feedback', 'keywords')) {
                $table->json('keywords')->nullable()->comment('Selected keyword tags');
            }

            // Add detailed feedback text
            if (!Schema::hasColumn('feedback', 'feedback_text')) {
                $table->text('feedback_text')->nullable()->comment('Detailed review from modal');
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
        Schema::table('feedback', function (Blueprint $table) {
            $table->dropForeign(['task_id']);
            $table->dropForeign(['appointment_id']);
            $table->dropForeign(['employee_id']);
            $table->dropColumn([
                'task_id',
                'appointment_id',
                'employee_id',
                'user_type',
                'rating',
                'keywords',
                'feedback_text'
            ]);
        });
    }
};
