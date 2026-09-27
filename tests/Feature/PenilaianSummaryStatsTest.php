<?php

namespace Tests\Feature;

use App\Models\AnggotaKelompok;
use App\Models\KelompokPpl;
use App\Models\Mitra;
use App\Models\PenilaianPpl;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tests\Traits\CreatesTestPplData;

class PenilaianSummaryStatsTest extends TestCase
{
    use RefreshDatabase, CreatesTestPplData;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->createTestPplData();

        $this->admin = $this->adminUser;
    }

    public function test_admin_penilaian_page_renders_summary_stat_cards_and_grade_distribution(): void
    {
        $dpl = User::where('role', 'dpl')->first();
        $mitra = Mitra::first();

        $ketuaUser = User::create([
            'username' => 'kelompok_stats_user',
            'password' => Hash::make('password'),
            'role' => 'ketua_kelompok',
            'nama_lengkap' => 'Akun Kelompok Test Stats',
            'is_active' => true,
        ]);

        $kelompok = KelompokPpl::create([
            'nama_kelompok' => 'Kelompok Test Stats',
            'tahun_akademik' => '2025/2026',
            'ketua_user_id' => $ketuaUser->id,
            'dpl_id' => $dpl->id,
            'mitra_id' => $mitra->id,
        ]);

        $mhs = AnggotaKelompok::create([
            'kelompok_id' => $kelompok->id,
            'nim' => '2023111222',
            'nama' => 'Mahasiswa Test Stats',
            'prodi' => 'Manajemen',
            'jenis_kelamin' => 'Laki-laki',
        ]);

        PenilaianPpl::create([
            'anggota_kelompok_id' => $mhs->id,
            'kelompok_id' => $kelompok->id,
            'nilai_mitra' => 90,
            'nilai_dpl' => 85,
            'status' => 'locked',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/penilaian');

        $response->assertStatus(200);
        $response->assertSee('Status Nilai Mhs');
        $response->assertSee('Input Nilai per Sumber');
        $response->assertSee('Final: 1');
        $response->assertSee('Rata-Rata Nilai PPL');
        $response->assertSee('Distribusi Huruf Mutu Mahasiswa');
        $response->assertSee('88.00'); // 90*0.6 + 85*0.4 = 54 + 34 = 88.00
    }
}
