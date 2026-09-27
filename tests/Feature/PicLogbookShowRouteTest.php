<?php

namespace Tests\Feature;

use App\Models\KegiatanHarian;
use App\Models\KelompokPpl;
use App\Models\Mitra;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PicLogbookShowRouteTest extends TestCase
{
    use RefreshDatabase;

    protected User $pic;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->pic = User::create([
            'username' => 'pic_test_route',
            'password' => Hash::make('password'),
            'role' => 'pic_mitra',
            'nama_lengkap' => 'PIC Test Route',
            'is_active' => true,
        ]);
    }

    public function test_pic_can_access_logbook_index_and_show(): void
    {
        $mitra = Mitra::create([
            'nama_mitra' => 'Instansi Test Route',
            'kategori' => 'SKPD',
            'pic_user_id' => $this->pic->id,
        ]);

        $ketuaUser = User::create([
            'username' => 'ketua_logbook_route',
            'password' => Hash::make('password'),
            'role' => 'ketua_kelompok',
            'nama_lengkap' => 'Ketua Kelompok Route',
            'is_active' => true,
        ]);

        $dpl = User::where('role', 'dpl')->first() ?? User::create([
            'username' => 'dpl_test_route',
            'password' => Hash::make('password'),
            'role' => 'dpl',
            'nama_lengkap' => 'DPL Route Test',
            'is_active' => true,
        ]);

        $kelompok = KelompokPpl::create([
            'nama_kelompok' => 'Kelompok Route Test',
            'tahun_akademik' => '2025/2026',
            'ketua_user_id' => $ketuaUser->id,
            'dpl_id' => $dpl->id,
            'mitra_id' => $mitra->id,
        ]);

        $logbook = KegiatanHarian::create([
            'kelompok_id' => $kelompok->id,
            'tanggal' => now()->format('Y-m-d'),
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '16:00',
            'deskripsi_kegiatan' => 'Kegiatan magang harian di instansi mitra.',
            'foto_dokumentasi' => 'logbook/foto_test.jpg',
            'terlambat' => false,
            'dilihat_mitra' => false,
        ]);

        $indexResponse = $this->actingAs($this->pic)->get('/pic/logbook');
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Pemantauan Logbook Mahasiswa Magang');
        $indexResponse->assertSee(route('pic.logbook.show', $logbook));

        $showResponse = $this->actingAs($this->pic)->get(route('pic.logbook.show', $logbook));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Detail Logbook Harian PPL');
        $showResponse->assertSee('Kegiatan magang harian di instansi mitra.');
    }
}
