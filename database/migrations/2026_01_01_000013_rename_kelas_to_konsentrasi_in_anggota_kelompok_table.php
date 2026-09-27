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
        Schema::table('anggota_kelompok', function (Blueprint $table) {
            if (Schema::hasColumn('anggota_kelompok', 'kelas')) {
                $table->renameColumn('kelas', 'konsentrasi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anggota_kelompok', function (Blueprint $table) {
            if (Schema::hasColumn('anggota_kelompok', 'konsentrasi')) {
                $table->renameColumn('konsentrasi', 'kelas');
            }
        });
    }
};
