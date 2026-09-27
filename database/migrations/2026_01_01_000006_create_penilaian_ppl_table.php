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
        Schema::create('penilaian_ppl', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_kelompok_id')->unique()->constrained('anggota_kelompok')->cascadeOnDelete();
            $table->foreignId('kelompok_id')->constrained('kelompok_ppl')->cascadeOnDelete();
            
            // Skor PIC Mitra (60% Weight) — 4 Komponen per Mahasiswa
            $table->decimal('mitra_skor_kedisiplinan', 5, 2)->nullable();
            $table->decimal('mitra_skor_etika', 5, 2)->nullable();
            $table->decimal('mitra_skor_kerjasama', 5, 2)->nullable();
            $table->decimal('mitra_skor_hasil_kerja', 5, 2)->nullable();
            $table->decimal('total_nilai_mitra', 5, 2)->nullable();
            $table->text('catatan_mitra')->nullable();
            
            // Skor DPL (40% Weight) — 4 Komponen per Mahasiswa
            $table->decimal('dpl_skor_kedisiplinan', 5, 2)->nullable();
            $table->decimal('dpl_skor_etika', 5, 2)->nullable();
            $table->decimal('dpl_skor_kerjasama', 5, 2)->nullable();
            $table->decimal('dpl_skor_hasil_kerja', 5, 2)->nullable();
            $table->decimal('total_nilai_dpl', 5, 2)->nullable();
            $table->text('catatan_dpl')->nullable();
            
            // Generated Column (Stored): 60% Mitra + 40% DPL
            $table->decimal('nilai_akhir_angka', 5, 2)->storedAs('(total_nilai_mitra * 0.60) + (total_nilai_dpl * 0.40)')->nullable();
            $table->string('nilai_huruf', 2)->nullable();
            $table->timestamp('dinilai_at')->nullable();
            $table->timestamps();

            $table->index('kelompok_id', 'idx_kelompok_penilaian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian_ppl');
    }
};
