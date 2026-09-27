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
        Schema::table('kegiatan_harian', function (Blueprint $table) {
            $table->string('status_validasi_mitra')->default('sesuai')->after('dilihat_mitra_at');
            $table->text('catatan_mitra')->nullable()->after('status_validasi_mitra');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kegiatan_harian', function (Blueprint $table) {
            $table->dropColumn(['status_validasi_mitra', 'catatan_mitra']);
        });
    }
};
