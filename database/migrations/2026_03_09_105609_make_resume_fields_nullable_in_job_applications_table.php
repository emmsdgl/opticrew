<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->string('resume_path')->nullable()->change();
            $table->string('resume_original_name')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->string('resume_path')->nullable(false)->change();
            $table->string('resume_original_name')->nullable(false)->change();
        });
    }
};
