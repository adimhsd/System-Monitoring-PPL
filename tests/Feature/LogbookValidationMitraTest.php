<?php

namespace Tests\Feature;

use App\Models\KegiatanHarian;
use App\Models\KelompokPpl;
use App\Models\Mitra;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LogbookValidationMitraTest extends TestCase
{
    use RefreshDatabase;

    protected User $picUser;
    protected KegiatanHarian $logbook;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->picUser = User::create([
            'username' => 'pic_val_test',
            'password' => Hash::make('password'),
            'role' => 'pic_mitra',
            'nama_lengkap' => 'PIC Validasi Test',
            'must_change_password' => false,
            'is_active' => true,
        ]);

        $mitra = Mitra::create([
            'nama_mitra' => 'Mitra Validasi Test',
            'kategori' => 'SKPD',
            'pic_user_id' => $this->picUser->id,
        ]);

        $ketuaUser = User::create([
            'username' => 'ketua_val_test',
            'password' => Hash::make('password'),
            'role' => 'ketua_kelompok',
            'nama_lengkap' => 'Ketua Validasi Test',
            'must_change_password' => false,
            'is_active' => true,
        ]);

        $kelompok = KelompokPpl::create([
            'nama_kelompok' => 'Kelompok Validasi Test',
            'tahun_akademik' => '2025/2026',
            'ketua_user_id' => $ketuaUser->id,
            'mitra_id' => $mitra->id,
            'status' => 'aktif',
        ]);

        $this->logbook = KegiatanHarian::create([
            'kelompok_id' => $kelompok->id,
            'tanggal' => '2026-08-19',
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '16:00:00',
            'deskripsi_kegiatan' => 'Uji coba validasi kegiatan harian.',
            'foto_dokumentasi' => 'logbooks/sample.jpg',
            'dilihat_mitra' => false,
            'dilihat_dpl' => false,
        ]);
    }

    public function test_pic_mitra_can_approve_logbook_with_sesuai_status(): void
    {
        $response = $this->actingAs($this->picUser)->post("/pic/logbook/{$this->logbook->id}/viewed", [
            'status_validasi_mitra' => 'sesuai',
            'catatan_mitra' => 'Kegiatan valid dan sesuai dengan tugas lapangan.',
        ]);

        $response->assertRedirect();

        $this->logbook->refresh();
        $this->assertTrue($this->logbook->dilihat_mitra);
        $this->assertEquals('sesuai', $this->logbook->status_validasi_mitra);
        $this->assertEquals('Kegiatan valid dan sesuai dengan tugas lapangan.', $this->logbook->catatan_mitra);
    }

    public function test_pic_mitra_can_approve_logbook_with_tidak_sesuai_status(): void
    {
        $response = $this->actingAs($this->picUser)->post("/pic/logbook/{$this->logbook->id}/viewed", [
            'status_validasi_mitra' => 'tidak_sesuai',
            'catatan_mitra' => 'Foto bukti tidak jelas, mohon diunggah ulang.',
        ]);

        $response->assertRedirect();

        $this->logbook->refresh();
        $this->assertTrue($this->logbook->dilihat_mitra);
        $this->assertEquals('tidak_sesuai', $this->logbook->status_validasi_mitra);
        $this->assertEquals('Foto bukti tidak jelas, mohon diunggah ulang.', $this->logbook->catatan_mitra);
    }
}
