<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Formulir pendaftaran publik (tanpa login) untuk mahasiswa PPL (Reguler / MBKM)
 * dan calon DPL. Data diverifikasi Admin sebelum menjadi data master.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendaftaran_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis_ppl', ['reguler', 'mbkm']);
            $table->string('nim', 20);
            $table->string('nama', 100);
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->enum('prodi', ['Manajemen', 'Akuntansi', 'Bisnis Digital']);
            $table->string('konsentrasi', 50)->nullable();
            $table->string('no_hp', 20);
            $table->string('email', 100)->nullable();
            $table->text('alamat')->nullable();

            // Khusus MBKM
            $table->string('program_mbkm', 100)->nullable();
            $table->string('instansi_mbkm', 150)->nullable();

            $table->string('file_bukti_pembayaran');

            // Verifikasi Admin: menunggu | diterima | ditolak
            $table->string('status', 10)->default('menunggu');
            $table->text('catatan_admin')->nullable();
            $table->foreignId('anggota_kelompok_id')->nullable()->constrained('anggota_kelompok')->nullOnDelete();
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('diverifikasi_at')->nullable();
            $table->timestamps();

            $table->index('nim');
            $table->index('status');
        });

        Schema::create('pendaftaran_dpl', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap', 100);
            $table->string('nip_nidn', 30);
            $table->string('no_hp', 20);
            $table->string('email', 100);
            $table->string('file_surat_kesanggupan');

            $table->string('status', 10)->default('menunggu');
            $table->text('catatan_admin')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('diverifikasi_at')->nullable();
            $table->timestamps();

            $table->index('nip_nidn');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_dpl');
        Schema::dropIfExists('pendaftaran_mahasiswa');
    }
};
