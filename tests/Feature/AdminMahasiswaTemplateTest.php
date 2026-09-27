<?php

namespace Tests\Feature;

use App\Exports\MahasiswaTemplateExport;
use App\Imports\MahasiswaImport;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMahasiswaTemplateTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('role', 'admin')->first();
    }

    public function test_admin_can_download_mahasiswa_import_template(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/mahasiswa/template');

        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename=Template_Import_Mahasiswa_PPL.xlsx');
    }

    public function test_admin_can_create_mahasiswa_with_enum_prodi_and_konsentrasi(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/mahasiswa', [
            'nim' => '2023081099',
            'nama' => 'Budi Permana',
            'jenis_kelamin' => 'Laki-laki',
            'prodi' => 'Bisnis Digital',
            'konsentrasi' => 'Bisnis Digital',
            'no_hp' => '08123456789',
            'alamat' => 'Kuningan',
        ]);

        $response->assertRedirect('/admin/mahasiswa');
        $this->assertDatabaseHas('anggota_kelompok', [
            'nim' => '2023081099',
            'prodi' => 'Bisnis Digital',
            'konsentrasi' => 'Bisnis Digital',
        ]);
    }

    public function test_mahasiswa_import_handles_template_columns_properly(): void
    {
        $templateExport = new MahasiswaTemplateExport();
        $headings = $templateExport->headings();
        $sampleRows = $templateExport->collection();

        $this->assertCount(2, $sampleRows);
        $this->assertEquals(['ID Mahasiswa', 'NIM', 'Nama Mahasiswa', 'Jenis Kelamin', 'Program Studi', 'Konsentrasi', 'No HP / Whatsapp', 'Alamat'], $headings);

        $import = new MahasiswaImport();
        $mhsModel = $import->model([
            'id_mahasiswa' => '',
            'nim' => '2023081100',
            'nama_mahasiswa' => 'Siska Amelia',
            'jenis_kelamin' => 'Perempuan',
            'program_studi' => 'Manajemen',
            'konsentrasi' => 'Keuangan',
            'no_hp_whatsapp' => '085211223344',
            'alamat' => 'Jl. Pemuda No. 10',
        ]);

        $this->assertNotNull($mhsModel);
        $this->assertInstanceOf(Mahasiswa::class, $mhsModel);
        $this->assertEquals('2023081100', $mhsModel->nim);
        $this->assertEquals('Manajemen', $mhsModel->prodi);
        $this->assertEquals('Keuangan', $mhsModel->konsentrasi);
    }
}
