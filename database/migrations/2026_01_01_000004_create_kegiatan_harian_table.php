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
        Schema::create('kegiatan_harian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelompok_id')->constrained('kelompok_ppl')->cascadeOnDelete();
            $table->date('tanggal');
            $table->time('waktu_mulai');
            $table->time('waktu_selesai');
            $table->text('deskripsi_kegiatan');
            $table->string('foto_dokumentasi', 255);
            $table->boolean('dilihat_mitra')->default(false);
            $table->timestamp('dilihat_mitra_at')->nullable();
            $table->boolean('dilihat_dpl')->default(false);
            $table->timestamp('dilihat_dpl_at')->nullable();
            $table->boolean('terlambat')->default(false);
            $table->timestamps();

            $table->unique(['kelompok_id', 'tanggal'], 'uniq_kelompok_tanggal');
            $table->index(['dilihat_mitra', 'dilihat_dpl'], 'idx_dilihat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kegiatan_harian');
    }
};
