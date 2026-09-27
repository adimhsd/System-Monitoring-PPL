<?php

namespace Tests\Feature;

use App\Models\KelompokPpl;
use App\Models\LuaranKelompok;
use App\Models\Mitra;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tests\Traits\CreatesTestPplData;

class LuaranSummaryStatsTest extends TestCase
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

    public function test_admin_luaran_page_renders_summary_stat_cards(): void
    {
        $dpl = User::where('role', 'dpl')->first();
        $mitra = Mitra::first();

        $ketuaUser = User::create([
            'username' => 'kelompok_luaran_user',
            'password' => Hash::make('password'),
            'role' => 'ketua_kelompok',
            'nama_lengkap' => 'Akun Kelompok Luaran Test',
            'is_active' => true,
        ]);

        $kelompok = KelompokPpl::create([
            'nama_kelompok' => 'Kelompok Luaran Test',
            'tahun_akademik' => '2025/2026',
            'ketua_user_id' => $ketuaUser->id,
            'dpl_id' => $dpl->id,
            'mitra_id' => $mitra->id,
        ]);

        LuaranKelompok::create([
            'kelompok_id' => $kelompok->id,
            'file_laporan_pdf' => 'luaran/kelompok_1/laporan.pdf',
            'url_video' => 'https://youtube.com/watch?v=12345678',
            'uploaded_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/luaran');

        $response->assertStatus(200);
        $response->assertSee('Status Luaran Kelompok');
        $response->assertSee('Laporan PDF Terkumpul');
        $response->assertSee('Video YouTube PPL');
        $response->assertSee('Progres Kelengkapan');
        $response->assertSee('Lengkap');
    }
}
