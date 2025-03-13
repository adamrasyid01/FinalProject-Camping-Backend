<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('camping_site_scores', function (Blueprint $table) {
            $table->decimal('normalized_score', 8, 4)->nullable()->after('ahp_score');
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('camping_site_scores', function (Blueprint $table) {
            //
        });
    }
};
