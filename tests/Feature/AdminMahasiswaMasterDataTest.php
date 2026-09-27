<?php

namespace Tests\Feature;

use App\Imports\MahasiswaImport;
use App\Models\KelompokPpl;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMahasiswaMasterDataTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected KelompokPpl $kelompok;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('role', 'admin')->first();
        $this->kelompok = KelompokPpl::first();
    }

    public function test_admin_can_view_mahasiswa_index(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/mahasiswa');

        $response->assertStatus(200);
        $response->assertSee('Master Data Mahasiswa');
    }

    public function test_admin_can_create_mahasiswa_manually(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/mahasiswa', [
            'nim' => '2026999901',
            'nama' => 'Budi Santoso',
            'jenis_kelamin' => 'Laki-laki',
            'prodi' => 'Bisnis Digital',
            'konsentrasi' => 'Keuangan',
            'no_hp' => '081299998888',
            'alamat' => 'Jl. Siliwangi No. 10 Kuningan',
            'kelompok_id' => $this->kelompok->id,
        ]);

        $response->assertRedirect('/admin/mahasiswa');
        $this->assertDatabaseHas('anggota_kelompok', [
            'nim' => '2026999901',
            'nama' => 'Budi Santoso',
            'jenis_kelamin' => 'Laki-laki',
            'prodi' => 'Bisnis Digital',
            'konsentrasi' => 'Keuangan',
            'no_hp' => '081299998888',
            'kelompok_id' => $this->kelompok->id,
        ]);
    }

    public function test_admin_can_update_mahasiswa_data(): void
    {
        $mhs = Mahasiswa::first();

        $response = $this->actingAs($this->admin)->put("/admin/mahasiswa/{$mhs->id}", [
            'nim' => $mhs->nim,
            'nama' => 'Siti Nurhaliza',
            'jenis_kelamin' => 'Perempuan',
            'prodi' => 'Akuntansi',
            'konsentrasi' => 'Pemasaran',
            'no_hp' => '089911112222',
            'alamat' => 'Alamat baru',
            'kelompok_id' => null,
        ]);

        $response->assertRedirect('/admin/mahasiswa');
        $this->assertDatabaseHas('anggota_kelompok', [
            'id' => $mhs->id,
            'nama' => 'Siti Nurhaliza',
            'jenis_kelamin' => 'Perempuan',
            'prodi' => 'Akuntansi',
            'konsentrasi' => 'Pemasaran',
        ]);
    }

    public function test_admin_can_delete_mahasiswa(): void
    {
        $mhs = Mahasiswa::first();

        $response = $this->actingAs($this->admin)->delete("/admin/mahasiswa/{$mhs->id}");

        $response->assertRedirect('/admin/mahasiswa');
        $this->assertDatabaseMissing('anggota_kelompok', [
            'id' => $mhs->id,
        ]);
    }

    public function test_admin_can_export_mahasiswa_excel(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/mahasiswa/export');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_mahasiswa_import_mapping_works_with_kelompok_linking(): void
    {
        $import = new MahasiswaImport();
        $import->model([
            'nim' => '2026888801',
            'nama_mahasiswa' => 'Mahasiswa Test Impor Kelompok',
            'jenis_kelamin' => 'Perempuan',
            'program_studi' => 'Bisnis Digital',
            'konsentrasi' => 'Akuntansi Syariah',
            'no_hp_whatsapp' => '087711223344',
            'alamat' => 'Jl. Veteran Kuningan',
            'kelompok_ppl' => $this->kelompok->nama_kelompok . ' - BAPPEDA',
        ]);

        $this->assertDatabaseHas('anggota_kelompok', [
            'nim' => '2026888801',
            'nama' => 'Mahasiswa Test Impor Kelompok',
            'jenis_kelamin' => 'Perempuan',
            'prodi' => 'Bisnis Digital',
            'konsentrasi' => 'Akuntansi Syariah',
            'no_hp' => '087711223344',
            'kelompok_id' => $this->kelompok->id,
        ]);
    }
}
