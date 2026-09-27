<?php

namespace Tests\Feature;

use App\Models\KelompokPpl;
use App\Models\Mitra;
use App\Models\MonitoringDpl;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use Tests\TestCase;

class MonitoringDplTest extends TestCase
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

        Storage::fake('public');

        $this->admin = User::where('role', 'admin')->first();

        $this->dpl = User::create([
            'username' => 'dpl_monitoring_test',
            'password' => bcrypt('password'),
            'role' => 'dpl',
            'nama_lengkap' => 'DPL Monitoring Test',
            'is_active' => true,
        ]);

        $this->ketuaUser = User::create([
            'username' => 'ketua_monitoring_test',
            'password' => bcrypt('password'),
            'role' => 'ketua_kelompok',
            'nama_lengkap' => 'Ketua Monitoring Test',
            'is_active' => true,
        ]);

        $mitra = Mitra::create([
            'nama_mitra' => 'Mitra Monitoring Test',
            'kategori' => 'SKPD',
        ]);

        $this->kelompok = KelompokPpl::create([
            'nama_kelompok' => 'Kelompok Monitoring Test',
            'dpl_id' => $this->dpl->id,
            'ketua_user_id' => $this->ketuaUser->id,
            'mitra_id' => $mitra->id,
            'tahun_akademik' => '2026/2027',
            'status' => 'aktif',
        ]);
    }

    public function test_dpl_can_view_monitoring_index(): void
    {
        $response = $this->actingAs($this->dpl)->get('/dpl/monitoring');

        $response->assertStatus(200);
        $response->assertSee('Monitoring & Kunjungan Lapangan DPL');
    }

    public function test_dpl_can_create_visit_report(): void
    {
        $file = UploadedFile::fake()->image('kunjungan_penyerahan.jpg', 600, 400);

        $response = $this->actingAs($this->dpl)->post('/dpl/monitoring', [
            'kelompok_id' => $this->kelompok->id,
            'jenis_kunjungan' => 'penyerahan',
            'tanggal_kunjungan' => '2026-08-22',
            'waktu_kunjungan' => '09:00',
            'catatan_kunjungan' => 'Melakukan penyerahan 5 mahasiswa PPL ke lokasi SKPD Mitra.',
            'foto_kunjungan' => $file,
        ]);

        $response->assertRedirect('/dpl/monitoring');
        $this->assertDatabaseHas('monitoring_dpl', [
            'dpl_user_id' => $this->dpl->id,
            'kelompok_id' => $this->kelompok->id,
            'jenis_kunjungan' => 'penyerahan',
            'disetujui_kelompok' => false,
        ]);
    }

    public function test_student_can_approve_dpl_visit_report(): void
    {
        $monitoring = MonitoringDpl::create([
            'dpl_user_id' => $this->dpl->id,
            'kelompok_id' => $this->kelompok->id,
            'jenis_kunjungan' => 'penyerahan',
            'tanggal_kunjungan' => '2026-08-22',
            'catatan_kunjungan' => 'Catatan Kunjungan Test Approval',
            'foto_kunjungan' => 'monitoring_dpl/test.jpg',
            'disetujui_kelompok' => false,
        ]);

        $response = $this->actingAs($this->ketuaUser)->post("/ketua/monitoring/{$monitoring->id}/approve");

        $response->assertRedirect();
        $this->assertDatabaseHas('monitoring_dpl', [
            'id' => $monitoring->id,
            'disetujui_kelompok' => true,
        ]);
    }

    public function test_admin_can_view_monitoring_rekap_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/monitoring');

        $response->assertStatus(200);
        $response->assertSee('Rekapitulasi Monitoring');
    }
}
