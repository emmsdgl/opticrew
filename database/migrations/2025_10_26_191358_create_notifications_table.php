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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Who receives this notification
            $table->string('type'); // Type of notification (e.g., 'appointment_approved', 'task_assigned')
            $table->string('title'); // Short heading for the notification
            $table->text('message'); // Full notification message
            $table->json('data')->nullable(); // Additional data (appointment_id, task_id, links, etc.)
            $table->timestamp('read_at')->nullable(); // When the notification was marked as read
            $table->timestamps();

            // Foreign key to users table
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Index for faster queries
            $table->index(['user_id', 'read_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
