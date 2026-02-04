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
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('location');
            $table->string('salary');
            $table->enum('type', ['full-time', 'part-time', 'remote'])->default('full-time');
            $table->string('type_badge')->default('Full-time Employee');
            $table->string('icon')->default('fa-user-tie');
            $table->string('icon_color')->default('blue');
            $table->boolean('is_active')->default(true);
            $table->json('required_skills')->nullable();
            $table->json('required_docs')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_postings');
    }
};
