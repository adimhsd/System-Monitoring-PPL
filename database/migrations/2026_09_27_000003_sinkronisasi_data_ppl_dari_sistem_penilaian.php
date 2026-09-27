<?php

use App\Models\KelompokPpl;
use App\Models\User;
use Database\Seeders\SinkronisasiDataPenilaianSeeder;
use Illuminate\Database\Migrations\Migration;

/**
 * Menerapkan pembaruan data dari SystemPenilaianPPL (ploting final & MBKM)
 * ke database Monitoring yang sudah berisi data. Pada database kosong
 * (instalasi baru / testing) migrasi ini dilewati; gunakan PplRealDataSeeder.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (User::where('role', 'dpl')->doesntExist() || KelompokPpl::doesntExist()) {
            return;
        }

        (new SinkronisasiDataPenilaianSeeder())->run();
    }

    public function down(): void
    {
        // Perubahan data tidak dibatalkan otomatis.
    }
};
