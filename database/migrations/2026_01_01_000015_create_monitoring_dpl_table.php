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
        Schema::create('monitoring_dpl', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dpl_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('kelompok_id')->constrained('kelompok_ppl')->cascadeOnDelete();
            $table->enum('jenis_kunjungan', ['penyerahan', 'penarikan', 'kunjungan_rutin'])->default('penyerahan');
            $table->date('tanggal_kunjungan');
            $table->time('waktu_kunjungan')->nullable();
            $table->text('catatan_kunjungan');
            $table->string('foto_kunjungan');
            $table->boolean('disetujui_kelompok')->default(false);
            $table->timestamp('tanggal_disetujui')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoring_dpl');
    }
};
