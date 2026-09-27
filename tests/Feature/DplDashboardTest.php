<?php

namespace Tests\Feature;

use App\Models\KegiatanHarian;
use App\Models\KelompokPpl;
use App\Models\Mitra;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DplDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $dpl;
    protected KelompokPpl $kelompok;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->dpl = User::where('role', 'dpl')->first() ?? User::create([
            'username' => 'dpl_dash_test',
            'password' => Hash::make('password'),
            'role' => 'dpl',
            'nama_lengkap' => 'DPL Dashboard Test',
            'must_change_password' => false,
            'is_active' => true,
        ]);

        $ketuaUser = User::create([
            'username' => 'ketua_dpl_dash_test',
            'password' => Hash::make('password'),
            'role' => 'ketua_kelompok',
            'nama_lengkap' => 'Ketua DPL Dash Test',
            'must_change_password' => false,
            'is_active' => true,
        ]);

        $mitra = Mitra::create([
            'nama_mitra' => 'Mitra DPL Dash Test',
            'kategori' => 'SKPD',
        ]);

        $this->kelompok = KelompokPpl::create([
            'nama_kelompok' => 'Kelompok DPL Dash Test',
            'tahun_akademik' => '2025/2026',
            'ketua_user_id' => $ketuaUser->id,
            'dpl_id' => $this->dpl->id,
            'mitra_id' => $mitra->id,
            'status' => 'aktif',
        ]);
    }

    public function test_dpl_can_access_dpl_dashboard(): void
    {
        $response = $this->actingAs($this->dpl)->get('/dpl/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Kelompok DPL Dash Test');
        $response->assertSee('Lihat Logbook');
    }
}
