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
        // Tambahkan kolom hanya jika belum ada
        Schema::table('camping_sites', function (Blueprint $table) {
            if (!Schema::hasColumn('camping_sites', 'text_reviews')) {
                $table->json('text_reviews')->nullable();
            }
            if (!Schema::hasColumn('camping_sites', 'total_sentimen')) {
                $table->json('total_sentimen')->nullable();
            }
        });

        // Rename kolom reviews ke total_reviews dengan raw SQL (untuk MariaDB lama)
        DB::statement('ALTER TABLE camping_sites CHANGE reviews total_reviews INT');
    }

    public function down()
    {
        Schema::table('camping_sites', function (Blueprint $table) {
            if (Schema::hasColumn('camping_sites', 'text_reviews')) {
                $table->dropColumn('text_reviews');
            }
            if (Schema::hasColumn('camping_sites', 'total_sentimen')) {
                $table->dropColumn('total_sentimen');
            }
        });

        DB::statement('ALTER TABLE camping_sites CHANGE total_reviews reviews INT');
    }
};
