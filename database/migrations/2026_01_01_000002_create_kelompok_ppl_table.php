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
        Schema::create('kelompok_ppl', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelompok', 100);
            $table->foreignId('mitra_id')->nullable()->constrained('mitra')->nullOnDelete();
            $table->foreignId('dpl_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('ketua_user_id')->constrained('users');
            $table->string('tahun_akademik', 10);
            $table->enum('status', ['aktif', 'selesai', 'dibatalkan'])->default('aktif');
            $table->timestamps();

            $table->index('dpl_id', 'idx_dpl');
            $table->index('tahun_akademik', 'idx_tahun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelompok_ppl');
    }
};
