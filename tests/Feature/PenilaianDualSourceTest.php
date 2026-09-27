<?php

namespace Tests\Feature;

use App\Models\AnggotaKelompok;
use App\Models\ConfigAplikasi;
use App\Models\KelompokPpl;
use App\Models\Mitra;
use App\Models\PenilaianPpl;
use App\Models\User;
use App\Services\RekapNilaiExportService;
use App\Services\PenilaianService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Alur penilaian gabungan: PIC Mitra mengisi Nilai Mitra (60%),
 * DPL mengisi Nilai Laporan (40%), status Draft / Terkunci (SystemPenilaianPPL).
 */
class PenilaianDualSourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $dpl;
    protected User $dplLain;
    protected User $pic;
    protected User $picLain;
    protected KelompokPpl $kelompok;
    protected KelompokPpl $kelompokLain;
    protected AnggotaKelompok $mhs;
    protected AnggotaKelompok $mhsLain;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('role', 'admin')->first();
        $this->dpl = $this->buatUser('dpl_nilai_a', 'dpl');
        $this->dplLain = $this->buatUser('dpl_nilai_b', 'dpl');
        $this->pic = $this->buatUser('pic_nilai_a', 'pic_mitra');
        $this->picLain = $this->buatUser('pic_nilai_b', 'pic_mitra');

        $this->kelompok = $this->buatKelompok('Kelompok Nilai A', $this->dpl, $this->pic);
        $this->kelompokLain = $this->buatKelompok('Kelompok Nilai B', $this->dplLain, $this->picLain);

        $this->mhs = $this->buatMahasiswa($this->kelompok, '2026100001');
        $this->mhsLain = $this->buatMahasiswa($this->kelompokLain, '2026100002');
    }

    private function buatUser(string $username, string $role): User
    {
        return User::create([
            'username' => $username,
            'password' => Hash::make('password'),
            'role' => $role,
            'nama_lengkap' => strtoupper($username),
            'must_change_password' => false,
            'is_active' => true,
        ]);
    }

    private function buatKelompok(string $nama, User $dpl, User $pic): KelompokPpl
    {
        $mitra = Mitra::create([
            'nama_mitra' => 'Mitra ' . $nama,
            'kategori' => 'SKPD',
            'pic_user_id' => $pic->id,
        ]);

        return KelompokPpl::create([
            'nama_kelompok' => $nama,
            'mitra_id' => $mitra->id,
            'dpl_id' => $dpl->id,
            'ketua_user_id' => $this->buatUser('ketua_' . str_replace(' ', '_', strtolower($nama)), 'ketua_kelompok')->id,
            'tahun_akademik' => '2026/2027',
            'status' => 'aktif',
        ]);
    }

    private function buatMahasiswa(KelompokPpl $kelompok, string $nim): AnggotaKelompok
    {
        return AnggotaKelompok::create([
            'kelompok_id' => $kelompok->id,
            'nim' => $nim,
            'nama' => 'Mahasiswa ' . $nim,
            'jenis_kelamin' => 'Perempuan',
            'prodi' => 'Manajemen',
        ]);
    }

    private function inputMitra(float $nilai, ?AnggotaKelompok $mhs = null)
    {
        $mhs ??= $this->mhs;

        return $this->actingAs($this->pic)->post("/pic/penilaian/{$mhs->kelompok_id}", [
            'nilai' => [$mhs->id => ['nilai_mitra' => $nilai, 'catatan_mitra' => 'Rajin.']],
        ]);
    }

    private function inputDpl(float $nilai, string $status = 'draft', ?AnggotaKelompok $mhs = null)
    {
        $mhs ??= $this->mhs;

        return $this->actingAs($this->dpl)->put("/dpl/penilaian/{$mhs->id}", [
            'nilai_dpl' => $nilai,
            'catatan_dpl' => 'Laporan lengkap.',
            'status' => $status,
        ]);
    }

    public function test_pic_mitra_can_input_nilai_mitra_sixty_percent(): void
    {
        $this->inputMitra(90)->assertRedirect('/pic/penilaian');

        $p = $this->mhs->fresh()->penilaian;
        $this->assertEquals(90.0, $p->nilai_mitra);
        $this->assertSame('Rajin.', $p->catatan_mitra);
        $this->assertNull($p->nilai_akhir, 'Nilai akhir belum terbentuk sebelum Nilai DPL diisi.');
        $this->assertSame('draft', $p->status);
    }

    public function test_dpl_input_calculates_final_score_and_letter_grade(): void
    {
        $this->inputMitra(90);
        $this->inputDpl(80)->assertRedirect();

        // (90 x 60%) + (80 x 40%) = 54 + 32 = 86.00 => A
        $this->assertDatabaseHas('penilaian_ppl', [
            'anggota_kelompok_id' => $this->mhs->id,
            'nilai_mitra' => 90.00,
            'nilai_dpl' => 80.00,
            'nilai_akhir' => 86.00,
            'nilai_huruf' => 'A',
            'status' => 'draft',
        ]);
    }

    public function test_dpl_cannot_grade_student_outside_bimbingan(): void
    {
        $this->inputDpl(80, 'draft', $this->mhsLain)->assertForbidden();
        $this->assertDatabaseMissing('penilaian_ppl', ['anggota_kelompok_id' => $this->mhsLain->id]);
    }

    public function test_pic_cannot_grade_other_mitra_and_ignores_foreign_students(): void
    {
        $this->actingAs($this->pic)->post("/pic/penilaian/{$this->kelompokLain->id}", [
            'nilai' => [$this->mhsLain->id => ['nilai_mitra' => 70]],
        ])->assertForbidden();

        // Mahasiswa kelompok lain yang diselipkan ke form kelompok sendiri diabaikan
        $this->actingAs($this->pic)->post("/pic/penilaian/{$this->kelompok->id}", [
            'nilai' => [$this->mhsLain->id => ['nilai_mitra' => 70]],
        ])->assertRedirect('/pic/penilaian');

        $this->assertDatabaseMissing('penilaian_ppl', ['anggota_kelompok_id' => $this->mhsLain->id]);
    }

    public function test_lock_requires_complete_scores(): void
    {
        $this->inputDpl(80, 'locked')->assertSessionHasErrors('status');
        $this->assertDatabaseMissing('penilaian_ppl', ['anggota_kelompok_id' => $this->mhs->id]);

        $this->actingAs($this->dpl)->post("/dpl/penilaian/{$this->mhs->id}/lock")->assertSessionHas('error');
    }

    public function test_locked_score_blocks_dpl_and_pic_until_admin_unlocks(): void
    {
        $this->inputMitra(90);
        $this->inputDpl(80);

        $this->actingAs($this->dpl)->post("/dpl/penilaian/{$this->mhs->id}/lock")->assertSessionHas('success');
        $p = $this->mhs->fresh()->penilaian;
        $this->assertTrue($p->isLocked());
        $this->assertSame($this->dpl->id, $p->locked_by);

        // DPL & PIC tidak bisa mengubah nilai terkunci
        $this->inputDpl(50)->assertSessionHasErrors('nilai');
        $this->inputMitra(40)->assertSessionHas('success');
        $p->refresh();
        $this->assertEquals(80.0, $p->nilai_dpl);
        $this->assertEquals(90.0, $p->nilai_mitra);

        // DPL tidak punya akses buka kunci
        $this->actingAs($this->dpl)->post("/admin/penilaian/{$this->mhs->id}/unlock")->assertRedirect('/dpl/dashboard');
        $this->assertTrue($p->fresh()->isLocked());

        // Admin membuka kunci, DPL dapat merevisi kembali
        $this->actingAs($this->admin)->post("/admin/penilaian/{$this->mhs->id}/unlock")->assertSessionHas('success');
        $this->assertFalse($p->fresh()->isLocked());

        $this->inputDpl(70)->assertSessionHasNoErrors();
        $this->assertEquals(82.0, $p->fresh()->nilai_akhir); // 54 + 28
    }

    public function test_admin_can_input_both_scores_and_edit_locked_score(): void
    {
        $this->actingAs($this->admin)->put("/admin/penilaian/{$this->mhs->id}", [
            'nilai_mitra' => 70,
            'nilai_dpl' => 60,
            'status' => 'locked',
        ])->assertSessionHasNoErrors();

        $p = $this->mhs->fresh()->penilaian;
        $this->assertTrue($p->isLocked());
        $this->assertEquals(66.0, $p->nilai_akhir); // 42 + 24
        $this->assertSame('BC', $p->nilai_huruf);

        $this->actingAs($this->admin)->put("/admin/penilaian/{$this->mhs->id}", [
            'nilai_mitra' => 70,
            'nilai_dpl' => 90,
            'status' => 'locked',
        ])->assertSessionHasNoErrors();

        $this->assertEquals(78.0, $p->fresh()->nilai_akhir);
        $this->assertSame('AB', $p->fresh()->nilai_huruf);
    }

    public function test_dpl_bulk_lock_only_affects_own_complete_scores(): void
    {
        $mhs2 = $this->buatMahasiswa($this->kelompok, '2026100003');

        $this->inputMitra(90);
        $this->inputDpl(80);
        $this->inputDpl(75, 'draft', $mhs2); // belum ada nilai mitra

        PenilaianService::simpan($this->mhsLain, ['nilai_mitra' => 80, 'nilai_dpl' => 80], $this->admin);

        $this->actingAs($this->dpl)->post('/dpl/penilaian/bulk-lock', [
            'ids' => [$this->mhs->id, $mhs2->id, $this->mhsLain->id],
        ])->assertSessionHas('success');

        $this->assertTrue($this->mhs->fresh()->penilaian->isLocked());
        $this->assertFalse($mhs2->fresh()->penilaian->isLocked());
        $this->assertFalse($this->mhsLain->fresh()->penilaian->isLocked());
    }

    public function test_admin_bulk_lock_and_unlock(): void
    {
        PenilaianService::simpan($this->mhs, ['nilai_mitra' => 80, 'nilai_dpl' => 80], $this->admin);
        PenilaianService::simpan($this->mhsLain, ['nilai_mitra' => 70, 'nilai_dpl' => 70], $this->admin);
        $ids = [$this->mhs->id, $this->mhsLain->id];

        $this->actingAs($this->admin)->post('/admin/penilaian/bulk', ['aksi' => 'lock', 'ids' => $ids]);
        $this->assertSame(2, PenilaianPpl::where('status', 'locked')->count());

        $this->actingAs($this->admin)->post('/admin/penilaian/bulk', ['aksi' => 'unlock', 'ids' => $ids]);
        $this->assertSame(0, PenilaianPpl::where('status', 'locked')->count());
    }

    public function test_grade_conversion_rules_according_to_feb_standard(): void
    {
        $this->assertEquals('A', PenilaianPpl::konversiNilaiHuruf(85.00));
        $this->assertEquals('A', PenilaianPpl::konversiNilaiHuruf(81.00));
        $this->assertEquals('AB', PenilaianPpl::konversiNilaiHuruf(77.50));
        $this->assertEquals('B', PenilaianPpl::konversiNilaiHuruf(72.00));
        $this->assertEquals('BC', PenilaianPpl::konversiNilaiHuruf(65.00));
        $this->assertEquals('C', PenilaianPpl::konversiNilaiHuruf(60.00));
        $this->assertEquals('CD', PenilaianPpl::konversiNilaiHuruf(53.50));
        $this->assertEquals('D', PenilaianPpl::konversiNilaiHuruf(48.00));
        $this->assertEquals('E', PenilaianPpl::konversiNilaiHuruf(30.00));

        $this->assertNull(PenilaianPpl::hitungNilaiAkhir(90, null));
        $this->assertEquals(86.0, PenilaianPpl::hitungNilaiAkhir(90, 80));
    }

    public function test_admin_grade_scale_update_recalculates_letter_grades(): void
    {
        PenilaianService::simpan($this->mhs, ['nilai_mitra' => 80, 'nilai_dpl' => 80], $this->admin);
        $this->assertSame('AB', $this->mhs->fresh()->penilaian->nilai_huruf);

        $skala = PenilaianPpl::SKALA_DEFAULT;
        $skala[0]['min'] = 80.00;
        $skala[1]['max'] = 79.99;

        $this->actingAs($this->admin)->post('/admin/penilaian/scale', ['skala' => $skala])
            ->assertRedirect('/admin/penilaian');

        $this->assertEquals(80.00, ConfigAplikasi::get('skala_nilai_huruf')[0]['min']);
        $this->assertSame('A', $this->mhs->fresh()->penilaian->nilai_huruf);
    }

    public function test_input_pages_render_for_each_role(): void
    {
        $this->inputMitra(90);
        $this->inputDpl(80);

        $this->actingAs($this->admin)->get('/admin/penilaian')
            ->assertOk()
            ->assertSee('Input & Rekap Nilai PPL Mahasiswa', false)
            ->assertSee('Standar Konversi Huruf Mutu')
            ->assertSee($this->mhs->nama)
            ->assertSee($this->mhsLain->nama);

        $this->actingAs($this->dpl)->get('/dpl/penilaian')
            ->assertOk()
            ->assertSee($this->mhs->nama)
            ->assertDontSee($this->mhsLain->nama)
            ->assertSee('86.00');

        $this->actingAs($this->dpl)->get("/dpl/penilaian?kelompok_id={$this->kelompok->id}")
            ->assertOk()
            ->assertSee('Luaran Kelompok Nilai A');

        $this->actingAs($this->pic)->get('/pic/penilaian')
            ->assertOk()
            ->assertSee($this->mhs->nama)
            ->assertDontSee($this->mhsLain->nama);

        // Tautan lama per kelompok diarahkan ke daftar terfilter
        $this->actingAs($this->dpl)->get("/dpl/penilaian/{$this->kelompok->id}/edit")
            ->assertRedirect("/dpl/penilaian?kelompok_id={$this->kelompok->id}");
    }

    public function test_status_filter_on_admin_page(): void
    {
        PenilaianService::simpan($this->mhs, ['nilai_mitra' => 80, 'nilai_dpl' => 80, 'status' => 'locked'], $this->admin);

        $this->actingAs($this->admin)->get('/admin/penilaian?status=locked')
            ->assertSee($this->mhs->nama)
            ->assertDontSee($this->mhsLain->nama);

        $this->actingAs($this->admin)->get('/admin/penilaian?status=belum')
            ->assertSee($this->mhsLain->nama)
            ->assertDontSee($this->mhs->nama);
    }

    public function test_excel_export_for_admin_and_dpl(): void
    {
        PenilaianService::simpan($this->mhs, ['nilai_mitra' => 90, 'nilai_dpl' => 80, 'status' => 'locked'], $this->admin);

        $this->actingAs($this->admin)->get('/admin/penilaian/export')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $this->actingAs($this->dpl)->get('/dpl/penilaian/export')->assertOk();

        $sheet = RekapNilaiExportService::buildSpreadsheet(PenilaianService::queryMahasiswa($this->dpl))->getActiveSheet();
        $this->assertSame('2026100001', $sheet->getCell('B6')->getValue());
        $this->assertEquals(86.0, $sheet->getCell('J6')->getValue());
        $this->assertSame('A', $sheet->getCell('K6')->getValue());
        $this->assertSame('Final (Terkunci)', $sheet->getCell('L6')->getValue());
        $this->assertNull($sheet->getCell('B7')->getValue(), 'Export DPL hanya memuat mahasiswa bimbingannya.');
    }

    public function test_dashboards_reflect_penilaian_progress(): void
    {
        $this->inputMitra(90);
        $this->inputDpl(80);

        $this->actingAs($this->dpl)->get('/dpl/dashboard')->assertOk()->assertViewHas('penilaianDoneCount', 1);
        $this->actingAs($this->pic)->get('/pic/dashboard')->assertOk()->assertViewHas('penilaianMitraDone', true);
        $this->actingAs($this->admin)->get('/admin/dashboard')->assertOk()
            ->assertViewHas('rekapPenilaian', fn ($r) => $r['mhs_sudah'] === 1 && $r['rata_rata'] == 86.0);
    }
}
