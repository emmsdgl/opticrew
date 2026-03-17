<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->string('status')->default('published')->after('is_active');
        });

        // Set existing active postings to 'published' and inactive to 'inactive'
        \DB::table('job_postings')->where('is_active', true)->update(['status' => 'published']);
        \DB::table('job_postings')->where('is_active', false)->update(['status' => 'inactive']);
    }

    public function down()
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
