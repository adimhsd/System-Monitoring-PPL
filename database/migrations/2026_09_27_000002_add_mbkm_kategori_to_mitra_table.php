<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menambah kategori mitra "MBKM" untuk kelompok Rekognisi PPL MBKM
 * (data dari SystemPenilaianPPL).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mitra', function (Blueprint $table) {
            $table->enum('kategori', ['SKPD', 'Swasta', 'UMKM', 'MBKM'])->change();
        });
    }

    public function down(): void
    {
        Schema::table('mitra', function (Blueprint $table) {
            $table->enum('kategori', ['SKPD', 'Swasta', 'UMKM'])->change();
        });
    }
};
