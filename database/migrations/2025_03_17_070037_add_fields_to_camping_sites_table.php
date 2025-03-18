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
        Schema::table('camping_sites', function (Blueprint $table) {
            $table->string('link')->nullable()->after('rating'); // Tambahkan field link
            $table->integer('reviews')->default(0)->after('link'); // Tambahkan field reviews
            $table->string('phone')->nullable()->after('reviews'); // Tambahkan field phone
            $table->string('location')->after('phone'); // Tambahkan field location
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('camping_sites', function (Blueprint $table) {
            $table->dropColumn(['link', 'reviews', 'phone', 'location']);
        });
    }
};
