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
            // Drop and recreate old feedback fields as nullable
            $table->dropColumn([
                'service_type',
                'overall_rating',
                'quality_rating',
                'cleanliness_rating',
                'punctuality_rating',
                'professionalism_rating',
                'value_rating',
                'comments'
            ]);
        });

        Schema::table('feedback', function (Blueprint $table) {
            // Re-add the fields as nullable
            $table->string('service_type')->nullable()->after('client_id');
            $table->integer('overall_rating')->nullable()->after('service_type');
            $table->integer('quality_rating')->nullable()->after('overall_rating');
            $table->integer('cleanliness_rating')->nullable()->after('quality_rating');
            $table->integer('punctuality_rating')->nullable()->after('cleanliness_rating');
            $table->integer('professionalism_rating')->nullable()->after('punctuality_rating');
            $table->integer('value_rating')->nullable()->after('professionalism_rating');
            $table->text('comments')->nullable()->after('value_rating');
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
            // Drop the nullable fields
            $table->dropColumn([
                'service_type',
                'overall_rating',
                'quality_rating',
                'cleanliness_rating',
                'punctuality_rating',
                'professionalism_rating',
                'value_rating',
                'comments'
            ]);
        });

        Schema::table('feedback', function (Blueprint $table) {
            // Re-add as not nullable
            $table->string('service_type')->after('client_id');
            $table->integer('overall_rating')->after('service_type');
            $table->integer('quality_rating')->after('overall_rating');
            $table->integer('cleanliness_rating')->after('quality_rating');
            $table->integer('punctuality_rating')->after('cleanliness_rating');
            $table->integer('professionalism_rating')->after('punctuality_rating');
            $table->integer('value_rating')->after('professionalism_rating');
            $table->text('comments')->after('value_rating');
        });
    }
};
