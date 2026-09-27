<?php

namespace Tests\Feature;

use App\Exports\KelompokTemplateExport;
use App\Imports\KelompokImport;
use App\Models\KelompokPpl;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminKelompokTemplateTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('role', 'admin')->first();
    }

    public function test_admin_can_download_kelompok_import_template(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/kelompok/template');

        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename=Template_Import_Kelompok_PPL.xlsx');
    }

    public function test_admin_can_export_kelompok_excel(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/kelompok/export');

        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename=Master_Data_Kelompok_PPL.xlsx');
    }

    public function test_kelompok_import_handles_template_columns_properly(): void
    {
        $templateExport = new KelompokTemplateExport();
        $headings = $templateExport->headings();
        $sampleRows = $templateExport->collection();

        $this->assertCount(2, $sampleRows);
        $this->assertEquals(['ID Kelompok', 'Nama Kelompok', 'Tahun Akademik', 'Status Kelompok', 'Username Ketua (User)', 'Password Ketua', 'Nama DPL', 'Nama Mitra'], $headings);

        $import = new KelompokImport();
        $kelompokModel = $import->model([
            'id_kelompok' => '',
            'nama_kelompok' => 'Kelompok Test Import 99',
            'tahun_akademik' => '2026/2027',
            'status_kelompok' => 'aktif',
            'username_ketua_user' => 'kelompok_test_99',
            'password_ketua' => 'secret123',
            'nama_dpl' => '',
            'nama_mitra' => '',
        ]);

        $this->assertNotNull($kelompokModel);
        $this->assertInstanceOf(KelompokPpl::class, $kelompokModel);
        $this->assertEquals('Kelompok Test Import 99', $kelompokModel->nama_kelompok);

        $ketuaUser = User::find($kelompokModel->ketua_user_id);
        $this->assertNotNull($ketuaUser);
        $this->assertEquals('kelompok_test_99', $ketuaUser->username);
    }
}
