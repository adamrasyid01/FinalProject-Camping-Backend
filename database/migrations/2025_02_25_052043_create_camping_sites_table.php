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
        Schema::create('camping_sites', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('location_id')->unsigned();
            $table->string('description');
            $table->string('image_url');
            $table->float('rating');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('camping_sites');
    }
};
