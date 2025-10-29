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
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->string('service_type'); // Final Cleaning or Deep Cleaning
            $table->integer('overall_rating'); // 1-5
            $table->integer('quality_rating'); // 1-5
            $table->integer('cleanliness_rating'); // 1-5
            $table->integer('punctuality_rating'); // 1-5
            $table->integer('professionalism_rating'); // 1-5
            $table->integer('value_rating'); // 1-5
            $table->text('comments');
            $table->boolean('would_recommend')->default(false);
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
        Schema::dropIfExists('feedback');
    }
};
