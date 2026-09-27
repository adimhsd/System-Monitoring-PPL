<?php

namespace Tests\Feature;

use App\Exports\MitraTemplateExport;
use App\Imports\MitraImport;
use App\Models\Mitra;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMitraTemplateTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('role', 'admin')->first();
    }

    public function test_admin_can_download_mitra_import_template(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/mitra/template');

        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename=Template_Import_Mitra_PPL.xlsx');
    }

    public function test_mitra_import_handles_template_columns_and_creates_pic(): void
    {
        $templateExport = new MitraTemplateExport();
        $headings = $templateExport->headings();
        $sampleRows = $templateExport->collection();

        $this->assertCount(2, $sampleRows);
        $this->assertEquals(['ID Mitra', 'Nama Mitra Instansi', 'Kategori', 'Alamat', 'Nama PIC Mitra', 'Username PIC', 'Password PIC', 'No HP PIC'], $headings);

        $import = new MitraImport();
        $mitraModel = $import->model([
            'id_mitra' => '',
            'nama_mitra_instansi' => 'PT Semangat Maju',
            'kategori' => 'Swasta',
            'alamat' => 'Jl. Merdeka No. 45',
            'nama_pic_mitra' => 'Budi Santoso',
            'username_pic' => 'pic_semangat',
            'password_pic' => 'secret123',
            'no_hp_pic_mitra' => '081234567890',
        ]);

        $this->assertNotNull($mitraModel);
        $this->assertInstanceOf(Mitra::class, $mitraModel);
        $this->assertEquals('PT Semangat Maju', $mitraModel->nama_mitra);
        $this->assertNotNull($mitraModel->pic_user_id);

        $picUser = User::find($mitraModel->pic_user_id);
        $this->assertNotNull($picUser);
        $this->assertEquals('pic_semangat', $picUser->username);
        $this->assertEquals('Budi Santoso', $picUser->nama_lengkap);
    }
}
