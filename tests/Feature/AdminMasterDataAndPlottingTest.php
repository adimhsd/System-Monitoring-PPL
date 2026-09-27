<?php

namespace Tests\Feature;

use App\Models\KelompokPpl;
use App\Models\Mitra;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMasterDataAndPlottingTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $dpl;
    protected User $pic;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('role', 'admin')->first();
        $this->dpl = User::where('role', 'dpl')->first();
        $this->pic = User::where('role', 'pic_mitra')->first();
    }

    public function test_admin_can_access_mitra_index(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/mitra');
        $response->assertStatus(200);
        $response->assertSee('Master Data Mitra');
    }

    public function test_admin_can_create_new_mitra_with_new_pic(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/mitra', [
            'nama_mitra' => 'Dinas Pendidikan Kuningan',
            'kategori' => 'SKPD',
            'alamat' => 'Jl. Siliwangi No. 10',
            'pic_nama' => 'Bambang Sukses',
            'pic_username' => 'pic_disdik',
            'pic_no_hp' => '081299998888',
        ]);

        $response->assertRedirect('/admin/mitra');
        $this->assertDatabaseHas('mitra', [
            'nama_mitra' => 'Dinas Pendidikan Kuningan',
            'kategori' => 'SKPD',
        ]);
        $this->assertDatabaseHas('users', [
            'username' => 'pic_disdik',
            'role' => 'pic_mitra',
        ]);
    }

    public function test_admin_can_plot_new_kelompok_with_students(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/kelompok', [
            'nama_kelompok' => 'Kelompok Test Independen',
            'username' => 'kelompok_independen_99',
            'password' => 'password123',
            'tahun_akademik' => '2025/2026',
            'status' => 'aktif',
        ]);

        $response->assertRedirect('/admin/kelompok');
        $this->assertDatabaseHas('kelompok_ppl', [
            'nama_kelompok' => 'Kelompok Test Independen',
        ]);
        $this->assertDatabaseHas('users', [
            'username' => 'kelompok_independen_99',
            'role' => 'ketua_kelompok',
        ]);
    }

    public function test_admin_can_reassign_kelompok_dpl(): void
    {
        $kelompok = KelompokPpl::first();

        $response = $this->actingAs($this->admin)->put("/admin/kelompok/{$kelompok->id}", [
            'nama_kelompok' => 'Kelompok Updated Test',
            'username' => 'ketua1_updated',
            'tahun_akademik' => $kelompok->tahun_akademik,
            'status' => 'aktif',
        ]);

        $response->assertRedirect('/admin/kelompok');
        $this->assertDatabaseHas('kelompok_ppl', [
            'id' => $kelompok->id,
            'nama_kelompok' => 'Kelompok Updated Test',
        ]);
    }

    public function test_admin_can_create_and_manage_users(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/users', [
            'username' => 'dpl_baru_test',
            'password' => 'password123',
            'role' => 'dpl',
            'nama_lengkap' => 'Dosen Baru M.Kom',
            'no_hp' => '081233445566',
            'nip_nidn' => '199501012020011001',
        ]);

        $response->assertRedirect('/admin/users');
        $this->assertDatabaseHas('users', [
            'username' => 'dpl_baru_test',
            'role' => 'dpl',
        ]);
    }
}
