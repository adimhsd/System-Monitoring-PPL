<?php

namespace Tests\Feature;

use App\Models\KelompokPpl;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CetakPdfAndExportExcelTest extends TestCase
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

    public function test_admin_can_download_lembar_nilai_pdf_per_kelompok(): void
    {
        $response = $this->actingAs($this->admin)->get("/admin/export/lembar-nilai-pdf/{$this->kelompok->id}");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_admin_can_download_rekap_nilai_pdf(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/export/rekap-nilai-pdf');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_admin_can_export_nilai_excel(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/export/nilai-excel');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_admin_can_export_kelompok_excel(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/export/kelompok-excel');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_admin_can_export_mitra_excel(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/export/mitra-excel');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}
