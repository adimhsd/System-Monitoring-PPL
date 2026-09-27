<?php

namespace Tests\Traits;

use App\Models\AnggotaKelompok;
use App\Models\KegiatanHarian;
use App\Models\KelompokPpl;
use App\Models\LuaranKelompok;
use App\Models\Mahasiswa;
use App\Models\Mitra;
use App\Models\Notifikasi;
use App\Models\PenilaianPpl;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

trait CreatesTestPplData
{
    protected User $adminUser;
    protected User $dplUser;
    protected User $picUser;
    protected User $ketuaUser;
    protected Mitra $testMitra;
    protected KelompokPpl $testKelompok;
    protected Mahasiswa $testMahasiswa;

    protected function createTestPplData(): void
    {
        $password = Hash::make('password');

        $this->adminUser = User::where('role', 'admin')->first() ?? User::create([
            'username' => 'admin_test',
            'password' => $password,
            'role' => 'admin',
            'nama_lengkap' => 'Admin Test',
            'must_change_password' => false,
            'is_active' => true,
        ]);

        $this->dplUser = User::where('role', 'dpl')->first() ?? User::create([
            'username' => 'dpl_test',
            'password' => $password,
            'role' => 'dpl',
            'nama_lengkap' => 'Dr. DPL Test, M.Si',
            'nip_nidn' => '198001012005011001',
            'must_change_password' => false,
            'is_active' => true,
        ]);

        $this->picUser = User::where('role', 'pic_mitra')->first() ?? User::create([
            'username' => 'pic_test',
            'password' => $password,
            'role' => 'pic_mitra',
            'nama_lengkap' => 'PIC Test',
            'must_change_password' => false,
            'is_active' => true,
        ]);

        $this->testMitra = Mitra::first() ?? Mitra::create([
            'nama_mitra' => 'Mitra Test',
            'kategori' => 'SKPD',
            'alamat' => 'Jl. Test No. 1',
            'pic_user_id' => $this->picUser->id,
        ]);

        $this->ketuaUser = User::where('role', 'ketua_kelompok')->first() ?? User::create([
            'username' => 'ketua_test',
            'password' => $password,
            'role' => 'ketua_kelompok',
            'nama_lengkap' => 'Kelompok 01 - Test',
            'must_change_password' => false,
            'is_active' => true,
        ]);

        $this->testKelompok = KelompokPpl::first() ?? KelompokPpl::create([
            'nama_kelompok' => 'Kelompok 01 - Test',
            'mitra_id' => $this->testMitra->id,
            'dpl_id' => $this->dplUser->id,
            'ketua_user_id' => $this->ketuaUser->id,
            'tahun_akademik' => '2025/2026',
            'status' => 'aktif',
        ]);

        $this->testMahasiswa = Mahasiswa::first() ?? Mahasiswa::create([
            'kelompok_id' => $this->testKelompok->id,
            'nim' => '2026000001',
            'nama' => 'Mahasiswa Test',
            'jenis_kelamin' => 'Laki-laki',
            'prodi' => 'Bisnis Digital',
            'konsentrasi' => 'Pemasaran Digital',
            'no_hp' => '081234567890',
        ]);
    }
}
