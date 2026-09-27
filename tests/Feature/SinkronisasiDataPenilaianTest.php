<?php

namespace Tests\Feature;

use App\Models\AnggotaKelompok;
use App\Models\KelompokPpl;
use App\Models\Mitra;
use App\Models\User;
use Database\Seeders\PplRealDataSeeder;
use Database\Seeders\SinkronisasiDataPenilaianSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Data riil hasil gabungan harus identik dengan SystemPenilaianPPL:
 * 470 mahasiswa, 92 kelompok (78 reguler + Kelompok 79 + 13 MBKM), 43 DPL.
 */
class SinkronisasiDataPenilaianTest extends TestCase
{
    use RefreshDatabase;

    public function test_real_data_seeder_matches_sistem_penilaian_dataset(): void
    {
        $this->seed(PplRealDataSeeder::class);

        $this->assertSame(470, AnggotaKelompok::whereNotNull('kelompok_id')->count());
        $this->assertSame(0, AnggotaKelompok::whereNull('kelompok_id')->count());
        $this->assertSame(92, KelompokPpl::count());
        $this->assertSame(43, User::where('role', 'dpl')->count());

        // Setiap kelompok memiliki akun ketua & mitra dengan PIC
        $this->assertSame(0, KelompokPpl::whereNull('ketua_user_id')->count());
        $this->assertSame('MBKM', Mitra::where('nama_mitra', 'MBKM')->value('kategori'));
        $this->assertNotNull(Mitra::where('nama_mitra', 'MBKM')->value('pic_user_id'));

        // Ploting final
        $this->assertSame('KELOMPOK 79', AnggotaKelompok::where('nim', '20230510122')->first()->kelompok->nama_kelompok);
        $this->assertSame('KELOMPOK 16', AnggotaKelompok::where('nim', '20230510219')->first()->kelompok->nama_kelompok);
        $this->assertSame('DPL_PPL24', KelompokPpl::where('nama_kelompok', 'KELOMPOK 08')->first()->dpl->username);
        $this->assertNull(AnggotaKelompok::where('nim', '20230510246')->first());

        // MBKM
        $this->assertSame(13, KelompokPpl::where('nama_kelompok', 'like', 'KELOMPOK MBKM%')->count());
        $this->assertSame('DPL_PPL42', KelompokPpl::where('nama_kelompok', 'KELOMPOK MBKM - Siti Nuke Nurfatimah')->first()->dpl->username);

        // Nama dengan tanda kutip ter-parse benar
        $this->assertSame("Salsa Ni'matul Maula", AnggotaKelompok::where('nim', '20230610122')->value('nama'));
    }

    public function test_sinkronisasi_is_idempotent(): void
    {
        $this->seed(PplRealDataSeeder::class);
        $users = User::count();

        $this->seed(SinkronisasiDataPenilaianSeeder::class);

        $this->assertSame($users, User::count());
        $this->assertSame(92, KelompokPpl::count());
        $this->assertSame(470, AnggotaKelompok::count());
    }

    public function test_sql_value_parser_handles_escapes_and_null(): void
    {
        $this->assertSame(
            ['1', "Ni'mah", null, 'a\\b', '0', 'x, y'],
            PplRealDataSeeder::parseValues("'1', 'Ni\\'mah', NULL, 'a\\\\b', '0', 'x, y'")
        );
    }

    public function test_admin_can_save_mitra_with_mbkm_category(): void
    {
        $this->seed();
        $admin = User::where('role', 'admin')->first();

        $this->actingAs($admin)->post('/admin/mitra', [
            'nama_mitra' => 'Program MBKM Test',
            'kategori' => 'MBKM',
            'alamat' => 'Kuningan',
            'pic_nama' => 'PIC MBKM Test',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('mitra', ['nama_mitra' => 'Program MBKM Test', 'kategori' => 'MBKM']);
    }
}
