<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Mengganti skema penilaian lama (4 komponen per sumber + generated column)
 * dengan skema SystemPenilaianPPL: satu Nilai Mitra (60%), satu Nilai Laporan
 * DPL (40%), Nilai Akhir, Huruf Mutu, dan status Draft / Terkunci.
 *
 * Nilai lama tetap dipertahankan: total_nilai_mitra → nilai_mitra dan
 * total_nilai_dpl → nilai_dpl.
 */
return new class extends Migration
{
    public function up(): void
    {
        $lama = Schema::hasTable('penilaian_ppl')
            ? DB::table('penilaian_ppl')->get()
            : collect();

        Schema::dropIfExists('penilaian_ppl');

        Schema::create('penilaian_ppl', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_kelompok_id')->unique()->constrained('anggota_kelompok')->cascadeOnDelete();
            $table->foreignId('kelompok_id')->nullable()->constrained('kelompok_ppl')->nullOnDelete();

            // Nilai Mitra / Lapangan (Bobot 60%) — diinput PIC Mitra
            $table->decimal('nilai_mitra', 5, 2)->nullable();
            $table->text('catatan_mitra')->nullable();
            $table->timestamp('dinilai_mitra_at')->nullable();

            // Nilai Laporan DPL (Bobot 40%) — diinput DPL
            $table->decimal('nilai_dpl', 5, 2)->nullable();
            $table->text('catatan_dpl')->nullable();
            $table->timestamp('dinilai_dpl_at')->nullable();

            // Hasil kalkulasi otomatis
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->string('nilai_huruf', 5)->nullable();

            // Status nilai: draft | locked
            $table->string('status', 10)->default('draft');
            $table->timestamp('locked_at')->nullable();
            $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index('kelompok_id', 'idx_kelompok_penilaian');
            $table->index('status', 'idx_status_penilaian');
        });

        foreach ($lama as $row) {
            $mitra = $row->total_nilai_mitra !== null ? (float) $row->total_nilai_mitra : null;
            $dpl = $row->total_nilai_dpl !== null ? (float) $row->total_nilai_dpl : null;
            $akhir = ($mitra !== null && $dpl !== null) ? round(($mitra * 0.60) + ($dpl * 0.40), 2) : null;

            DB::table('penilaian_ppl')->insert([
                'id' => $row->id,
                'anggota_kelompok_id' => $row->anggota_kelompok_id,
                'kelompok_id' => $row->kelompok_id,
                'nilai_mitra' => $mitra,
                'catatan_mitra' => $row->catatan_mitra,
                'dinilai_mitra_at' => $mitra !== null ? $row->dinilai_at : null,
                'nilai_dpl' => $dpl,
                'catatan_dpl' => $row->catatan_dpl,
                'dinilai_dpl_at' => $dpl !== null ? $row->dinilai_at : null,
                'nilai_akhir' => $akhir,
                'nilai_huruf' => $akhir !== null ? $row->nilai_huruf : null,
                'status' => 'draft',
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);
        }
    }

    public function down(): void
    {
        $baru = DB::table('penilaian_ppl')->get();

        Schema::dropIfExists('penilaian_ppl');

        Schema::create('penilaian_ppl', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_kelompok_id')->unique()->constrained('anggota_kelompok')->cascadeOnDelete();
            $table->foreignId('kelompok_id')->constrained('kelompok_ppl')->cascadeOnDelete();
            $table->decimal('mitra_skor_kedisiplinan', 5, 2)->nullable();
            $table->decimal('mitra_skor_etika', 5, 2)->nullable();
            $table->decimal('mitra_skor_kerjasama', 5, 2)->nullable();
            $table->decimal('mitra_skor_hasil_kerja', 5, 2)->nullable();
            $table->decimal('total_nilai_mitra', 5, 2)->nullable();
            $table->text('catatan_mitra')->nullable();
            $table->decimal('dpl_skor_kedisiplinan', 5, 2)->nullable();
            $table->decimal('dpl_skor_etika', 5, 2)->nullable();
            $table->decimal('dpl_skor_kerjasama', 5, 2)->nullable();
            $table->decimal('dpl_skor_hasil_kerja', 5, 2)->nullable();
            $table->decimal('total_nilai_dpl', 5, 2)->nullable();
            $table->text('catatan_dpl')->nullable();
            $table->decimal('nilai_akhir_angka', 5, 2)->storedAs('(total_nilai_mitra * 0.60) + (total_nilai_dpl * 0.40)')->nullable();
            $table->string('nilai_huruf', 2)->nullable();
            $table->timestamp('dinilai_at')->nullable();
            $table->timestamps();

            $table->index('kelompok_id', 'idx_kelompok_penilaian');
        });

        foreach ($baru as $row) {
            if ($row->kelompok_id === null) {
                continue;
            }

            DB::table('penilaian_ppl')->insert([
                'id' => $row->id,
                'anggota_kelompok_id' => $row->anggota_kelompok_id,
                'kelompok_id' => $row->kelompok_id,
                'total_nilai_mitra' => $row->nilai_mitra,
                'catatan_mitra' => $row->catatan_mitra,
                'total_nilai_dpl' => $row->nilai_dpl,
                'catatan_dpl' => $row->catatan_dpl,
                'nilai_huruf' => $row->nilai_huruf,
                'dinilai_at' => $row->dinilai_dpl_at ?? $row->dinilai_mitra_at,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);
        }
    }
};
