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
        Schema::create('luaran_kelompok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelompok_id')->unique()->constrained('kelompok_ppl')->cascadeOnDelete();
            $table->string('file_laporan_pdf', 255);
            $table->string('url_video', 255);
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('luaran_kelompok');
    }
};
