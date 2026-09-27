<?php

namespace Tests\Feature;

use App\Exports\DplTemplateExport;
use App\Imports\DplImport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminDplTemplateTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('role', 'admin')->first();
    }

    public function test_admin_can_download_dpl_import_template(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/dpl/template');

        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename=Template_Import_DPL_PPL.xlsx');
    }

    public function test_dpl_import_handles_template_columns_and_custom_password(): void
    {
        $templateExport = new DplTemplateExport();
        $headings = $templateExport->headings();
        $sampleRows = $templateExport->collection();

        $this->assertCount(2, $sampleRows);
        $this->assertEquals(['ID DPL', 'Username', 'Password', 'NIP / NIDN', 'Nama Lengkap DPL', 'No HP / Whatsapp', 'Email', 'Status Akun'], $headings);

        $import = new DplImport();
        $userModel = $import->model([
            'id_dpl' => '',
            'username' => 'DPL_TEMPLATE_USER',
            'password' => 'customSecret123',
            'nip_nidn' => '199988877766',
            'nama_lengkap_dpl' => 'Dr. Template Test, M.Kom',
            'no_hp_whatsapp' => '081299998888',
            'email' => 'template@uniku.ac.id',
            'status_akun' => 'Aktif',
        ]);

        $this->assertNotNull($userModel);
        $this->assertEquals('DPL_TEMPLATE_USER', $userModel->username);
        $this->assertTrue(Hash::check('customSecret123', $userModel->password));
    }
}
