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
        Schema::table('camping_sites', function (Blueprint $table) {
            $table->text('image_url')->change(); // Ubah dari VARCHAR ke TEXT
        });
    }

    public function down()
    {
        Schema::table('camping_sites', function (Blueprint $table) {
            $table->string('image_url', 255)->change(); // Kembalikan ke VARCHAR jika rollback
        });
    }
};
