<?php

namespace Tests\Feature;

use App\Models\KelompokPpl;
use App\Models\Mitra;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PicDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $picUser;
    protected Mitra $mitra;
    protected KelompokPpl $kelompok;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->picUser = User::create([
            'username' => 'pic_dash_test',
            'password' => Hash::make('password'),
            'role' => 'pic_mitra',
            'nama_lengkap' => 'PIC Dashboard Test',
            'must_change_password' => false,
            'is_active' => true,
        ]);

        $this->mitra = Mitra::create([
            'nama_mitra' => 'Mitra PIC Dash Test',
            'kategori' => 'SKPD',
            'pic_user_id' => $this->picUser->id,
        ]);

        $ketuaUser = User::create([
            'username' => 'ketua_pic_dash_test',
            'password' => Hash::make('password'),
            'role' => 'ketua_kelompok',
            'nama_lengkap' => 'Ketua PIC Dash Test',
            'must_change_password' => false,
            'is_active' => true,
        ]);

        $dpl = User::where('role', 'dpl')->first();

        $this->kelompok = KelompokPpl::create([
            'nama_kelompok' => 'Kelompok PIC Dash Test',
            'tahun_akademik' => '2025/2026',
            'ketua_user_id' => $ketuaUser->id,
            'dpl_id' => $dpl->id ?? null,
            'mitra_id' => $this->mitra->id,
            'status' => 'aktif',
        ]);
    }

    public function test_pic_mitra_can_access_pic_dashboard(): void
    {
        $response = $this->actingAs($this->picUser)->get('/pic/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Kelompok PIC Dash Test');
        $response->assertSee('Kelola Logbook Kegiatan');
    }
}
