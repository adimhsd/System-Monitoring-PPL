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
            if (!Schema::hasColumn('anggota_kelompok', 'konsentrasi') && !Schema::hasColumn('anggota_kelompok', 'kelas')) {
                $table->string('konsentrasi', 50)->nullable()->after('prodi');
            }
            if (!Schema::hasColumn('anggota_kelompok', 'no_hp')) {
                $table->string('no_hp', 20)->nullable();
            }
            if (!Schema::hasColumn('anggota_kelompok', 'alamat')) {
                $table->text('alamat')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anggota_kelompok', function (Blueprint $table) {
            if (Schema::hasColumn('anggota_kelompok', 'alamat')) {
                $table->dropColumn('alamat');
            }
            if (Schema::hasColumn('anggota_kelompok', 'no_hp')) {
                $table->dropColumn('no_hp');
            }
            if (Schema::hasColumn('anggota_kelompok', 'konsentrasi')) {
                $table->dropColumn('konsentrasi');
            }
            if (Schema::hasColumn('anggota_kelompok', 'kelas')) {
                $table->dropColumn('kelas');
            }
        });
    }
};
