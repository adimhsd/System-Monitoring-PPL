<?php

namespace Tests\Feature;

use App\Models\AnggotaKelompok;
use App\Models\KelompokPpl;
use App\Models\Mitra;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * - DPL & PIC Mitra hanya direkomendasikan (tidak diwajibkan) mengganti password default.
 * - Kelompok MBKM: tanpa logbook harian, Nilai Mitra diinput langsung oleh DPL.
 */
class MbkmDanPasswordOpsionalTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $dpl;
    protected User $pic;
    protected User $ketuaMbkm;
    protected User $ketuaReguler;
    protected KelompokPpl $kelompokMbkm;
    protected KelompokPpl $kelompokReguler;
    protected AnggotaKelompok $mhsMbkm;
    protected AnggotaKelompok $mhsReguler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('role', 'admin')->first();
        $this->dpl = $this->buatUser('dpl_mbkm', 'dpl');
        $this->pic = $this->buatUser('pic_mbkm_test', 'pic_mitra');
        $this->ketuaMbkm = $this->buatUser('ketua_mbkm', 'ketua_kelompok');
        $this->ketuaReguler = $this->buatUser('ketua_reguler', 'ketua_kelompok');

        $mitraMbkm = Mitra::create(['nama_mitra' => 'MBKM Test', 'kategori' => 'MBKM', 'pic_user_id' => $this->pic->id]);
        $mitraReguler = Mitra::create(['nama_mitra' => 'Dinas Test', 'kategori' => 'SKPD', 'pic_user_id' => $this->pic->id]);

        $this->kelompokMbkm = $this->buatKelompok('KELOMPOK MBKM - Test', $mitraMbkm, $this->ketuaMbkm);
        $this->kelompokReguler = $this->buatKelompok('KELOMPOK REGULER', $mitraReguler, $this->ketuaReguler);

        $this->mhsMbkm = $this->buatMahasiswa($this->kelompokMbkm, '2026200001');
        $this->mhsReguler = $this->buatMahasiswa($this->kelompokReguler, '2026200002');
    }

    private function buatUser(string $username, string $role, bool $wajibGanti = false): User
    {
        return User::create([
            'username' => $username,
            'password' => Hash::make('password'),
            'role' => $role,
            'nama_lengkap' => strtoupper($username),
            'must_change_password' => $wajibGanti,
            'is_active' => true,
        ]);
    }

    private function buatKelompok(string $nama, Mitra $mitra, User $ketua): KelompokPpl
    {
        return KelompokPpl::create([
            'nama_kelompok' => $nama,
            'mitra_id' => $mitra->id,
            'dpl_id' => $this->dpl->id,
            'ketua_user_id' => $ketua->id,
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
            'jenis_kelamin' => 'Laki-laki',
            'prodi' => 'Manajemen',
        ]);
    }

    // ---------- Ganti password opsional ----------

    public function test_dpl_and_pic_with_default_password_are_not_forced_to_change(): void
    {
        foreach (['dpl' => '/dpl/dashboard', 'pic_mitra' => '/pic/dashboard'] as $role => $dashboard) {
            $user = $this->buatUser('baru_' . $role, $role, true);

            $this->post('/login', ['username' => $user->username, 'password' => 'password'])
                ->assertRedirect($dashboard);

            $this->get($dashboard)
                ->assertOk()
                ->assertSee('Rekomendasi Keamanan')
                ->assertSee('tidak wajib');

            $this->post('/logout');
        }
    }

    public function test_recommendation_banner_disappears_after_password_changed(): void
    {
        $user = $this->buatUser('dpl_ganti', 'dpl', true);

        $this->actingAs($user)->post('/update-password', [
            'current_password' => 'password',
            'password' => 'PasswordBaru123',
            'password_confirmation' => 'PasswordBaru123',
        ]);

        $this->assertFalse($user->fresh()->must_change_password);
        $this->actingAs($user->fresh())->get('/dpl/dashboard')->assertDontSee('Rekomendasi Keamanan');
    }

    public function test_other_roles_are_still_forced_to_change_default_password(): void
    {
        $ketua = $this->buatUser('ketua_wajib', 'ketua_kelompok', true);

        $this->actingAs($ketua)->get('/ketua/dashboard')->assertRedirect(route('password.change'));
        $this->assertFalse($this->dpl->fresh()->wajibGantiPassword());
    }

    // ---------- Kelompok MBKM ----------

    public function test_mbkm_group_has_no_logbook_menu_or_access(): void
    {
        $this->assertTrue($this->kelompokMbkm->isMbkm());
        $this->assertFalse($this->kelompokReguler->isMbkm());

        $this->actingAs($this->ketuaMbkm)->get('/ketua/dashboard')
            ->assertOk()
            ->assertSee('Kelompok Rekognisi PPL MBKM')
            ->assertDontSee('Input Logbook Hari Ini')
            ->assertDontSee('Logbook Harian');

        foreach (['/ketua/logbook', '/ketua/logbook/create', '/student/logbook', '/ketua/logbook-pdf'] as $url) {
            $this->actingAs($this->ketuaMbkm)->get($url)->assertRedirect('/ketua/dashboard');
        }

        $this->actingAs($this->ketuaMbkm)->post('/ketua/logbook', [])->assertRedirect('/ketua/dashboard');
        $this->assertDatabaseMissing('kegiatan_harian', ['kelompok_id' => $this->kelompokMbkm->id]);
    }

    public function test_regular_group_still_uses_logbook(): void
    {
        $this->actingAs($this->ketuaReguler)->get('/ketua/dashboard')
            ->assertOk()
            ->assertSee('Input Logbook Hari Ini')
            ->assertSee('Logbook Harian');

        $this->actingAs($this->ketuaReguler)->get('/ketua/logbook')->assertOk();
    }

    public function test_dpl_inputs_nilai_mitra_for_mbkm_students(): void
    {
        $this->actingAs($this->dpl)->put("/dpl/penilaian/{$this->mhsMbkm->id}", [
            'nilai_mitra' => 90,
            'nilai_dpl' => 80,
            'status' => 'draft',
        ])->assertSessionHasNoErrors();

        $p = $this->mhsMbkm->fresh()->penilaian;
        $this->assertEquals(90.0, $p->nilai_mitra);
        $this->assertEquals(86.0, $p->nilai_akhir);

        // Nilai Mitra wajib untuk MBKM
        $this->actingAs($this->dpl)->put("/dpl/penilaian/{$this->mhsMbkm->id}", [
            'nilai_dpl' => 70,
            'status' => 'draft',
        ])->assertSessionHasErrors('nilai_mitra');
    }

    public function test_dpl_cannot_set_nilai_mitra_for_regular_students(): void
    {
        $this->actingAs($this->dpl)->put("/dpl/penilaian/{$this->mhsReguler->id}", [
            'nilai_mitra' => 95,
            'nilai_dpl' => 80,
            'status' => 'draft',
        ])->assertSessionHasNoErrors();

        $p = $this->mhsReguler->fresh()->penilaian;
        $this->assertNull($p->nilai_mitra);
        $this->assertEquals(80.0, $p->nilai_dpl);
    }

    public function test_pic_mitra_cannot_grade_mbkm_group(): void
    {
        $this->actingAs($this->pic)->get('/pic/penilaian')
            ->assertOk()
            ->assertDontSee('KELOMPOK MBKM - Test');

        $this->actingAs($this->pic)->post("/pic/penilaian/{$this->kelompokMbkm->id}", [
            'nilai' => [$this->mhsMbkm->id => ['nilai_mitra' => 70]],
        ])->assertForbidden();
    }

    public function test_mbkm_excluded_from_logbook_warnings(): void
    {
        $this->actingAs($this->admin)->get('/admin/dashboard')
            ->assertOk()
            ->assertViewHas('kelompokBelumIsiLogbook', fn ($list) => $list->pluck('id')->doesntContain($this->kelompokMbkm->id)
                && $list->pluck('id')->contains($this->kelompokReguler->id));

        $this->actingAs($this->dpl)->get('/dpl/dashboard')
            ->assertOk()
            ->assertSee('Kelompok MBKM tanpa logbook harian');
    }
}
