<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardSummaryReportsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('role', 'admin')->first();
    }

    public function test_admin_dashboard_renders_rekap_penilaian_and_rekap_luaran_sections(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Rekapitulasi Penilaian PPL Mahasiswa');
        $response->assertSee('Rekapitulasi Luaran Akhir PPL Fakultas');
        $response->assertSee('Detail Rekap →');
        $response->assertSee('Detail Luaran →');
    }
}
