<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('camping_sites', function (Blueprint $table) {
            $table->dropColumn('description'); // Hapus kolom image_url
        });
    }

    public function down(): void
    {
        Schema::table('camping_sites', function (Blueprint $table) {
            $table->text('description')->nullable(); // Jika rollback, tambahkan kembali
        });
    }
};