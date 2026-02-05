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
            // Drop the existing foreign key constraint
            $table->dropForeign(['client_id']);

            // Drop the column
            $table->dropColumn('client_id');
        });

        Schema::table('feedback', function (Blueprint $table) {
            // Re-add the column as nullable with foreign key
            $table->foreignId('client_id')->nullable()->after('id')->constrained('clients')->onDelete('cascade');
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
            // Drop the nullable foreign key
            $table->dropForeign(['client_id']);
            $table->dropColumn('client_id');
        });

        Schema::table('feedback', function (Blueprint $table) {
            // Re-add as not nullable
            $table->foreignId('client_id')->after('id')->constrained('clients')->onDelete('cascade');
        });
    }
};
