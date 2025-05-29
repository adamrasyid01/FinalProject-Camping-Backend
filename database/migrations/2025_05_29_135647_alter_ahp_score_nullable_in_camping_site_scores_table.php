<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('camping_site_scores', function (Blueprint $table) {
            // Ubah menjadi integer dan nullable
            $table->integer('ahp_score')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('camping_site_scores', function (Blueprint $table) {
            // Ubah kembali ke float dan tidak nullable (default sebelumnya)
            $table->float('ahp_score')->nullable(false)->change();
        });
    }
};
