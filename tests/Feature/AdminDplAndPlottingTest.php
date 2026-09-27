<?php

namespace Tests\Feature;

use App\Models\KelompokPpl;
use App\Models\Mahasiswa;
use App\Models\Mitra;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDplAndPlottingTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('role', 'admin')->first();
    }

    public function test_admin_can_view_dpl_index(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/dpl');

        $response->assertStatus(200);
        $response->assertSee('Master Data Dosen Pembimbing Lapangan');
    }

    public function test_admin_can_create_new_dpl(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/dpl', [
            'username' => 'dpl_baru_test',
            'password' => 'password123',
            'nama_lengkap' => 'Dr. Test DPL, M.Si.',
            'nip_nidn' => '0412345678',
            'no_hp' => '081234567890',
            'email' => 'dpltest@uniku.ac.id',
        ]);

        $response->assertRedirect('/admin/dpl');
        $this->assertDatabaseHas('users', [
            'username' => 'dpl_baru_test',
            'role' => 'dpl',
            'nama_lengkap' => 'Dr. Test DPL, M.Si.',
        ]);
    }

    public function test_admin_can_view_plotting_index(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/plotting');

        $response->assertStatus(200);
        $response->assertSee('Plotting');
    }

    public function test_admin_can_update_plotting_mapping(): void
    {
        $kelompok = KelompokPpl::first();
        $mitra = Mitra::first();
        $dpl = User::create([
            'username' => 'dpl_test_plotting',
            'password' => bcrypt('password'),
            'role' => 'dpl',
            'nama_lengkap' => 'DPL Test Plotting',
            'is_active' => true,
        ]);

        $mhs = Mahasiswa::create([
            'nim' => '2026777001',
            'nama' => 'Student Plotting Test',
            'jenis_kelamin' => 'Laki-laki',
            'prodi' => 'Manajemen',
        ]);

        $response = $this->actingAs($this->admin)->put("/admin/plotting/{$kelompok->id}", [
            'mitra_id' => $mitra->id,
            'dpl_id' => $dpl->id,
            'mahasiswa_ids' => [$mhs->id],
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/admin/plotting');
        $this->assertDatabaseHas('kelompok_ppl', [
            'id' => $kelompok->id,
            'mitra_id' => $mitra->id,
            'dpl_id' => $dpl->id,
        ]);
        $this->assertDatabaseHas('anggota_kelompok', [
            'id' => $mhs->id,
            'kelompok_id' => $kelompok->id,
        ]);
    }

    public function test_authenticated_user_can_view_pedoman_pdf(): void
    {
        $response = $this->actingAs($this->admin)->get('/pedoman');

        $response->assertStatus(200);
        $response->assertSee('Buku Panduan');
        $response->assertSee('1zWaxZW57ThQLwIZAZAxpyPqK9_WIpV5f');
    }

    public function test_dpl_load_validation_allows_up_to_30_students_and_rejects_above_30(): void
    {
        $kelompok1 = KelompokPpl::first();
        $mitra = Mitra::first();
        $dpl = User::create([
            'username' => 'dpl_capacity_test',
            'password' => bcrypt('password'),
            'role' => 'dpl',
            'nama_lengkap' => 'DPL Capacity Test',
            'is_active' => true,
        ]);

        // Create 25 existing students for DPL in another kelompok
        $ketuaUser = User::create([
            'username' => 'ketua_preloaded_test',
            'password' => bcrypt('password'),
            'role' => 'ketua_kelompok',
            'nama_lengkap' => 'Ketua Preloaded',
            'is_active' => true,
        ]);

        $kelompokOther = KelompokPpl::create([
            'nama_kelompok' => 'Kelompok Pre-loaded',
            'ketua_user_id' => $ketuaUser->id,
            'dpl_id' => $dpl->id,
            'tahun_akademik' => '2026/2027',
            'status' => 'aktif',
        ]);

        $mhsIds = [];
        for ($i = 1; $i <= 25; $i++) {
            $m = Mahasiswa::create([
                'nim' => "20269000{$i}",
                'nama' => "Preloaded Student {$i}",
                'jenis_kelamin' => 'Laki-laki',
                'prodi' => 'Manajemen',
                'kelompok_id' => $kelompokOther->id,
            ]);
            $mhsIds[] = $m->id;
        }

        // Create 6 new students (25 + 6 = 31 > 30 => should fail)
        $newMhsIds = [];
        for ($i = 1; $i <= 6; $i++) {
            $m = Mahasiswa::create([
                'nim' => "20269100{$i}",
                'nama' => "New Student {$i}",
                'jenis_kelamin' => 'Perempuan',
                'prodi' => 'Akuntansi',
            ]);
            $newMhsIds[] = $m->id;
        }

        // Attempting to assign 6 students (25 + 6 = 31 > 30) should return validation error
        $failResponse = $this->actingAs($this->admin)->put("/admin/plotting/{$kelompok1->id}", [
            'mitra_id' => $mitra->id,
            'dpl_id' => $dpl->id,
            'mahasiswa_ids' => $newMhsIds,
        ]);
        $failResponse->assertSessionHasErrors('dpl_id');

        // Assigning 5 students (25 + 5 = 30 <= 30) should succeed
        array_pop($newMhsIds);
        $passResponse = $this->actingAs($this->admin)->put("/admin/plotting/{$kelompok1->id}", [
            'mitra_id' => $mitra->id,
            'dpl_id' => $dpl->id,
            'mahasiswa_ids' => $newMhsIds,
        ]);
        $passResponse->assertSessionHasNoErrors();
        $passResponse->assertRedirect('/admin/plotting');
    }
}
