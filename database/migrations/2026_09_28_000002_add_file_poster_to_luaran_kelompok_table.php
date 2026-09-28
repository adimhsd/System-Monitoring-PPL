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
        Schema::table('luaran_kelompok', function (Blueprint $table) {
            $table->string('file_poster', 255)->nullable()->after('url_video');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('luaran_kelompok', function (Blueprint $table) {
            $table->dropColumn('file_poster');
        });
    }
};
