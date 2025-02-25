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
        Schema::create('user_preference_criterias', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_preference_id')->unsigned();
            $table->bigInteger('criteria_id')->unsigned();
            $table->float('weight');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_preference_criterias');
    }
};
