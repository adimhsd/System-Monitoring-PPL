<?php

namespace Tests\Feature;

use App\Models\KegiatanHarian;
use App\Models\KelompokPpl;
use App\Models\Mitra;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LogbookCetakPdfTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $dpl;
    protected User $ketuaUser;
    protected KelompokPpl $kelompok;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('role', 'admin')->first() ?? User::create([
            'username' => 'admin_test_pdf',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'nama_lengkap' => 'Admin PDF Test',
            'must_change_password' => false,
            'is_active' => true,
        ]);

        $this->dpl = User::where('role', 'dpl')->first() ?? User::create([
            'username' => 'dpl_test_pdf',
            'password' => Hash::make('password'),
            'role' => 'dpl',
            'nama_lengkap' => 'DPL PDF Test',
            'must_change_password' => false,
            'is_active' => true,
        ]);

        $this->ketuaUser = User::create([
            'username' => 'ketua_test_pdf',
            'password' => Hash::make('password'),
            'role' => 'ketua_kelompok',
            'nama_lengkap' => 'Ketua Kelompok PDF',
            'must_change_password' => false,
            'is_active' => true,
        ]);

        $picUser = User::create([
            'username' => 'pic_test_pdf',
            'password' => Hash::make('password'),
            'role' => 'pic_mitra',
            'nama_lengkap' => 'PIC Mitra Test PDF',
            'must_change_password' => false,
            'is_active' => true,
        ]);

        $mitra = Mitra::create([
            'nama_mitra' => 'Instansi Mitra Test PDF',
            'kategori' => 'SKPD',
            'pic_user_id' => $picUser->id,
        ]);

        $this->kelompok = KelompokPpl::create([
            'nama_kelompok' => 'Kelompok PDF Test',
            'tahun_akademik' => '2025/2026',
            'ketua_user_id' => $this->ketuaUser->id,
            'dpl_id' => $this->dpl->id,
            'mitra_id' => $mitra->id,
        ]);

        KegiatanHarian::create([
            'kelompok_id' => $this->kelompok->id,
            'tanggal' => now()->format('Y-m-d'),
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '16:00',
            'deskripsi_kegiatan' => 'Kegiatan magang harian untuk tes PDF.',
            'foto_dokumentasi' => 'logbook/foto_test.jpg',
            'terlambat' => false,
            'dilihat_mitra' => true,
            'dilihat_dpl' => true,
        ]);
    }

    public function test_ketua_kelompok_can_download_logbook_pdf(): void
    {
        $response = $this->actingAs($this->ketuaUser)->get('/ketua/logbook-pdf');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_dpl_can_download_logbook_pdf_for_assigned_group(): void
    {
        $response = $this->actingAs($this->dpl)->get("/dpl/logbook/{$this->kelompok->id}/pdf");

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_can_download_logbook_pdf_for_any_group(): void
    {
        $response = $this->actingAs($this->admin)->get("/admin/kelompok/{$this->kelompok->id}/logbook-pdf");

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
}
