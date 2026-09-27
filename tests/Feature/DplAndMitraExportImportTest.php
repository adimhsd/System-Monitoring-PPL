<?php

namespace Tests\Feature;

use App\Exports\DplExport;
use App\Exports\MitraExport;
use App\Models\Mitra;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class DplAndMitraExportImportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('role', 'admin')->first();
    }

    public function test_admin_can_export_dpl_excel(): void
    {
        Excel::fake();

        $response = $this->actingAs($this->admin)->get('/admin/dpl/export');

        $response->assertStatus(200);
        Excel::assertDownloaded('Master_Data_DPL_PPL.xlsx', function (DplExport $export) {
            return true;
        });
    }

    public function test_admin_can_import_dpl_excel(): void
    {
        $content = "username,nip_nidn,nama_lengkap_dpl,no_hp_whatsapp,email,password\n" .
                   "dpl_excel_1,198801012015011001,Dr. Excel DPL M.Si,081233334444,dplexcel@uniku.ac.id,secret123\n";

        $file = UploadedFile::fake()->createWithContent('dpl_import.csv', $content);

        $response = $this->actingAs($this->admin)->post('/admin/dpl/import', [
            'file_excel' => $file,
        ]);

        $response->assertRedirect('/admin/dpl');
        $this->assertDatabaseHas('users', [
            'username' => 'dpl_excel_1',
            'role' => 'dpl',
            'nama_lengkap' => 'Dr. Excel DPL M.Si',
            'nip_nidn' => '198801012015011001',
        ]);
    }

    public function test_admin_can_export_mitra_excel(): void
    {
        Excel::fake();

        $response = $this->actingAs($this->admin)->get('/admin/mitra/export');

        $response->assertStatus(200);
        Excel::assertDownloaded('Master_Data_Mitra_PPL.xlsx', function (MitraExport $export) {
            return true;
        });
    }

    public function test_admin_can_import_mitra_excel_with_pic_account_creation(): void
    {
        $content = "nama_mitra_instansi,kategori,alamat,username_pic,nama_pic_mitra,no_hp_pic_mitra\n" .
                   "PT Indofood Sukses Makmur,Swasta,Jl. Raya Indofood No. 8,pic_indofood,Surya Pratama,085566778899\n";

        $file = UploadedFile::fake()->createWithContent('mitra_import.csv', $content);

        $response = $this->actingAs($this->admin)->post('/admin/mitra/import', [
            'file_excel' => $file,
        ]);

        $response->assertRedirect('/admin/mitra');

        $this->assertDatabaseHas('users', [
            'username' => 'pic_indofood',
            'role' => 'pic_mitra',
            'nama_lengkap' => 'Surya Pratama',
        ]);

        $picUser = User::where('username', 'pic_indofood')->first();

        $this->assertDatabaseHas('mitra', [
            'nama_mitra' => 'PT Indofood Sukses Makmur',
            'kategori' => 'Swasta',
            'pic_user_id' => $picUser->id,
        ]);
    }
}
