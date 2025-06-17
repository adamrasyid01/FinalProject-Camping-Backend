<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
     public function up()
    {
        DB::statement('ALTER TABLE camping_site_scores CHANGE sentiment_percentage sentiment_value DOUBLE');
    }

    public function down()
    {
        DB::statement('ALTER TABLE camping_site_scores CHANGE sentiment_value sentiment_percentage DOUBLE');
    }
};
