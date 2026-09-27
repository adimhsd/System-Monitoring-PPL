<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminMahasiswaExportPdfTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('role', 'admin')->first() ?? User::create([
            'username' => 'admin_mhs_pdf_test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'nama_lengkap' => 'Admin Mahasiswa PDF Test',
            'must_change_password' => false,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_download_mahasiswa_pdf_report(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/mahasiswa/pdf');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
}
