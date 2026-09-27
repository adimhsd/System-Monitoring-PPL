<?php

namespace Database\Seeders;

use App\Models\AnggotaKelompok;
use App\Models\ConfigAplikasi;
use App\Models\KegiatanHarian;
use App\Models\KelompokPpl;
use App\Models\Mitra;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $password = Hash::make('password');

        // 1. Akun Admin Utama
        User::create([
            'username' => 'admin',
            'password' => $password,
            'role' => 'admin',
            'nama_lengkap' => 'Administrator Unit PPL FEB',
            'no_hp' => '081234567890',
            'nip_nidn' => '197501012000031001',
            'must_change_password' => false,
            'is_active' => true,
        ]);

        // 2. Config Konversi Skala Nilai Huruf Default
        ConfigAplikasi::set('skala_nilai_huruf', [
            ['min' => 81.00, 'max' => 100.00, 'huruf' => 'A'],
            ['min' => 75.00, 'max' => 80.99,  'huruf' => 'AB'],
            ['min' => 69.00, 'max' => 74.99,  'huruf' => 'B'],
            ['min' => 63.00, 'max' => 68.99,  'huruf' => 'BC'],
            ['min' => 57.00, 'max' => 62.99,  'huruf' => 'C'],
            ['min' => 51.00, 'max' => 56.99,  'huruf' => 'CD'],
            ['min' => 45.00, 'max' => 50.99,  'huruf' => 'D'],
            ['min' => 0.00,  'max' => 44.99,  'huruf' => 'E'],
        ]);

        // Jika dipanggil dari PHPUnit test suite, buatkan data pendukung pengujian
        if (app()->environment('testing')) {
            $this->seedTestFixtureData($password);
        }
    }

    private function seedTestFixtureData(string $password): void
    {
        $dpl1 = User::create([
            'username' => 'dpl1',
            'password' => $password,
            'role' => 'dpl',
            'nama_lengkap' => 'Dr. Ahmad Hidayat, M.Si',
            'no_hp' => '081298765432',
            'nip_nidn' => '198001012005011001',
            'must_change_password' => false,
            'is_active' => true,
        ]);

        $dpl2 = User::create([
            'username' => 'dpl2',
            'password' => $password,
            'role' => 'dpl',
            'nama_lengkap' => 'Sri Rahayu, S.E., M.Ak',
            'no_hp' => '081345678901',
            'nip_nidn' => '198503152010122002',
            'must_change_password' => false,
            'is_active' => true,
        ]);

        $pic1 = User::create([
            'username' => 'pic_bappeda',
            'password' => $password,
            'role' => 'pic_mitra',
            'nama_lengkap' => 'Haji Sobri, S.T.',
            'no_hp' => '085211223344',
            'nip_nidn' => '197802102003121002',
            'must_change_password' => false,
            'is_active' => true,
        ]);

        $mitra1 = Mitra::create([
            'nama_mitra' => 'BAPPEDA Kabupaten Kuningan',
            'kategori' => 'SKPD',
            'alamat' => 'Jl. Soekarno No. 1, Kuningan',
            'pic_user_id' => $pic1->id,
        ]);

        $ketua1 = User::create([
            'username' => 'ketua1',
            'password' => $password,
            'role' => 'ketua_kelompok',
            'nama_lengkap' => 'Kelompok 01 - BAPPEDA',
            'no_hp' => '087711223344',
            'must_change_password' => false,
            'is_active' => true,
        ]);

        $kelompok1 = KelompokPpl::create([
            'nama_kelompok' => 'Kelompok 01 - BAPPEDA',
            'mitra_id' => $mitra1->id,
            'dpl_id' => $dpl1->id,
            'ketua_user_id' => $ketua1->id,
            'tahun_akademik' => '2025/2026',
            'status' => 'aktif',
        ]);

        AnggotaKelompok::create([
            'kelompok_id' => $kelompok1->id,
            'nim' => '2022081001',
            'nama' => 'Rizky Pratama',
            'prodi' => 'Bisnis Digital',
            'konsentrasi' => 'Pemasaran Digital',
        ]);

        KegiatanHarian::create([
            'kelompok_id' => $kelompok1->id,
            'tanggal' => now()->subDays(2)->format('Y-m-d'),
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '16:00:00',
            'deskripsi_kegiatan' => 'Observasi lapangan dan pengenalan alur kerja unit perencanaan BAPPEDA.',
            'foto_dokumentasi' => 'logbooks/sample_foto_1.jpg',
            'dilihat_mitra' => true,
            'dilihat_mitra_at' => now()->subDays(2)->addHours(2),
            'dilihat_dpl' => true,
            'dilihat_dpl_at' => now()->subDays(2)->addHours(4),
            'terlambat' => false,
        ]);

        Notifikasi::create([
            'user_id' => $ketua1->id,
            'judul' => 'Pengingat Logbook',
            'pesan' => 'Jangan lupa mengisi logbook kegiatan harian PPL hari ini.',
            'tipe' => 'reminder',
            'is_read' => false,
        ]);
    }
}
