<?php

namespace Tests\Feature;

use App\Models\KelompokPpl;
use App\Models\Mitra;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $ketuaUser;
    protected KelompokPpl $kelompok;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->ketuaUser = User::create([
            'username' => 'ketua_dash_test',
            'password' => Hash::make('password'),
            'role' => 'ketua_kelompok',
            'nama_lengkap' => 'Ketua Dashboard Test',
            'must_change_password' => false,
            'is_active' => true,
        ]);

        $mitra = Mitra::create([
            'nama_mitra' => 'Mitra Dash Test',
            'kategori' => 'SKPD',
        ]);

        $this->kelompok = KelompokPpl::create([
            'nama_kelompok' => 'Kelompok Dashboard Test',
            'tahun_akademik' => '2025/2026',
            'ketua_user_id' => $this->ketuaUser->id,
            'mitra_id' => $mitra->id,
            'status' => 'aktif',
        ]);
    }

    public function test_student_can_access_student_dashboard(): void
    {
        $response = $this->actingAs($this->ketuaUser)->get('/student/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Kelompok Dashboard Test');
        $response->assertSee('Input Logbook Hari Ini');
    }

    public function test_student_can_access_ketua_dashboard(): void
    {
        $response = $this->actingAs($this->ketuaUser)->get('/ketua/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Kelompok Dashboard Test');
    }

    public function test_student_can_view_logbook_detail_page(): void
    {
        $logbook = \App\Models\KegiatanHarian::create([
            'kelompok_id' => $this->kelompok->id,
            'tanggal' => now()->format('Y-m-d'),
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '16:00',
            'deskripsi_kegiatan' => 'Kegiatan magang harian untuk tes detail.',
            'foto_dokumentasi' => 'logbook/foto_test.jpg',
            'terlambat' => false,
            'dilihat_mitra' => false,
            'dilihat_dpl' => false,
        ]);

        $response = $this->actingAs($this->ketuaUser)->get("/ketua/logbook/{$logbook->id}");

        $response->assertStatus(200);
        $response->assertSee('Kegiatan magang harian untuk tes detail.');
    }
}
