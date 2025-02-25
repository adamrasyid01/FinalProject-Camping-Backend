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
        Schema::create('camping_site_scores', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('camping_site_id')->unsigned();
            $table->bigInteger('criterion_id')->unsigned();
            $table->float('sentiment_percentage');
            $table->integer('ahp_score');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('camping_site_scores');
    }
};
