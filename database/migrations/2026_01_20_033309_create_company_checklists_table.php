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
        // Main checklist table
        Schema::create('company_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contracted_client_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('important_reminders')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Checklist categories (e.g., KITCHEN, BATHROOM, etc.)
        Schema::create('checklist_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checklist_id')->constrained('company_checklists')->onDelete('cascade');
            $table->string('name');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Checklist items within categories
        Schema::create('checklist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('checklist_categories')->onDelete('cascade');
            $table->string('name');
            $table->string('quantity')->nullable();
            $table->integer('sort_order')->default(0);
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
        Schema::dropIfExists('checklist_items');
        Schema::dropIfExists('checklist_categories');
        Schema::dropIfExists('company_checklists');
    }
};
