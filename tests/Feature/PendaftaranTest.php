<?php

namespace Tests\Feature;

use App\Models\AnggotaKelompok;
use App\Models\PendaftaranDpl;
use App\Models\PendaftaranMahasiswa;
use App\Models\User;
use App\Services\PendaftaranService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Formulir pendaftaran publik Mahasiswa PPL (Reguler/MBKM) & DPL, serta verifikasi Admin.
 */
class PendaftaranTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Storage::fake('local');
        config(['filesystems.disks.r2.key' => null]);

        $this->admin = User::where('role', 'admin')->first();
    }

    private function pdf(int $kb = 200, string $nama = 'bukti.pdf'): UploadedFile
    {
        return UploadedFile::fake()->create($nama, $kb, 'application/pdf');
    }

    private function dataMahasiswa(array $override = []): array
    {
        return array_merge([
            'jenis_ppl' => 'reguler',
            'nim' => '20240510001',
            'nama' => 'Rina Pendaftar',
            'jenis_kelamin' => 'Perempuan',
            'prodi' => 'Manajemen',
            'konsentrasi' => 'Pemasaran',
            'no_hp' => '081234567890',
            'email' => 'rina@gmail.com',
            'alamat' => 'Kuningan',
            'bukti_pembayaran' => $this->pdf(),
            'pernyataan' => '1',
        ], $override);
    }

    private function dataDpl(array $override = []): array
    {
        return array_merge([
            'nama_lengkap' => 'Dr. Calon DPL, S.E., M.M.',
            'nip_nidn' => '0412345678',
            'no_hp' => '081298765432',
            'email' => 'calon.dpl@uniku.ac.id',
            'surat_kesanggupan' => $this->pdf(500, 'surat.pdf'),
            'pernyataan' => '1',
        ], $override);
    }

    public function test_public_pages_are_accessible_without_login(): void
    {
        $this->get('/pendaftaran')->assertOk()->assertSee('Mahasiswa PPL')->assertSee('Dosen Pembimbing Lapangan');
        $this->get('/pendaftaran/mahasiswa')->assertOk()->assertSee('Bukti Pembayaran');
        $this->get('/pendaftaran/dpl')->assertOk()->assertSee('Surat Kesanggupan');
        $this->get('/login')->assertSee('Daftar PPL');
    }

    public function test_mahasiswa_reguler_can_register(): void
    {
        $this->post('/pendaftaran/mahasiswa', $this->dataMahasiswa())
            ->assertRedirect('/pendaftaran/selesai');

        $p = PendaftaranMahasiswa::first();
        $this->assertSame('menunggu', $p->status);
        $this->assertSame('reguler', $p->jenis_ppl);
        $this->assertNull($p->program_mbkm);
        Storage::disk('local')->assertExists($p->file_bukti_pembayaran);

        $this->get('/pendaftaran/selesai')->assertOk()->assertSee('MHS-00001');
    }

    public function test_mahasiswa_mbkm_requires_program_and_instansi(): void
    {
        $this->post('/pendaftaran/mahasiswa', $this->dataMahasiswa(['jenis_ppl' => 'mbkm']))
            ->assertSessionHasErrors(['program_mbkm', 'instansi_mbkm']);

        $this->post('/pendaftaran/mahasiswa', $this->dataMahasiswa([
            'jenis_ppl' => 'mbkm',
            'program_mbkm' => 'Studi Independen',
            'instansi_mbkm' => 'PT Startup Indonesia',
        ]))->assertRedirect('/pendaftaran/selesai');

        $this->assertDatabaseHas('pendaftaran_mahasiswa', ['jenis_ppl' => 'mbkm', 'instansi_mbkm' => 'PT Startup Indonesia']);
    }

    public function test_bukti_pembayaran_must_be_pdf_max_1mb(): void
    {
        $this->post('/pendaftaran/mahasiswa', $this->dataMahasiswa(['bukti_pembayaran' => $this->pdf(1025)]))
            ->assertSessionHasErrors(['bukti_pembayaran' => 'Ukuran file bukti pembayaran maksimal 1 MB.']);

        $this->post('/pendaftaran/mahasiswa', $this->dataMahasiswa([
            'bukti_pembayaran' => UploadedFile::fake()->image('bukti.jpg'),
        ]))->assertSessionHasErrors('bukti_pembayaran');

        $this->post('/pendaftaran/mahasiswa', $this->dataMahasiswa(['bukti_pembayaran' => $this->pdf(1024)]))
            ->assertSessionHasNoErrors();
    }

    public function test_duplicate_nim_rejected_unless_previous_registration_was_rejected(): void
    {
        $this->post('/pendaftaran/mahasiswa', $this->dataMahasiswa());
        $this->post('/pendaftaran/mahasiswa', $this->dataMahasiswa())->assertSessionHasErrors('nim');

        PendaftaranMahasiswa::first()->update(['status' => 'ditolak']);
        $this->post('/pendaftaran/mahasiswa', $this->dataMahasiswa())->assertSessionHasNoErrors();
        $this->assertSame(2, PendaftaranMahasiswa::count());
    }

    public function test_admin_accepts_mahasiswa_into_master_data(): void
    {
        $this->post('/pendaftaran/mahasiswa', $this->dataMahasiswa());
        $p = PendaftaranMahasiswa::first();

        $this->actingAs($this->admin)->get('/admin/pendaftaran/mahasiswa')->assertOk()->assertSee('Rina Pendaftar');
        $this->actingAs($this->admin)->get("/admin/pendaftaran/mahasiswa/{$p->id}/file")
            ->assertOk()->assertHeader('Content-Type', 'application/pdf');

        $this->actingAs($this->admin)->post("/admin/pendaftaran/mahasiswa/{$p->id}/terima")->assertSessionHas('success');

        $mhs = AnggotaKelompok::where('nim', '20240510001')->first();
        $this->assertNotNull($mhs);
        $this->assertNull($mhs->kelompok_id, 'Mahasiswa baru belum diplot ke kelompok.');
        $this->assertSame('Pemasaran', $mhs->konsentrasi);
        $this->assertSame('diterima', $p->fresh()->status);
        $this->assertSame($mhs->id, $p->fresh()->anggota_kelompok_id);

        // Tidak bisa diterima dua kali
        $this->actingAs($this->admin)->post("/admin/pendaftaran/mahasiswa/{$p->id}/terima")->assertSessionHasErrors('status');
    }

    public function test_admin_bulk_accept_and_reject_mahasiswa(): void
    {
        $this->post('/pendaftaran/mahasiswa', $this->dataMahasiswa());
        $this->post('/pendaftaran/mahasiswa', $this->dataMahasiswa(['nim' => '20240510002', 'nama' => 'Budi']));
        $this->post('/pendaftaran/mahasiswa', $this->dataMahasiswa(['nim' => '20240510003', 'nama' => 'Cici']));
        [$a, $b, $c] = PendaftaranMahasiswa::orderBy('id')->get()->all();

        $this->actingAs($this->admin)->post("/admin/pendaftaran/mahasiswa/{$c->id}/tolak", ['catatan_admin' => 'Nominal tidak sesuai']);
        $this->assertSame('ditolak', $c->fresh()->status);
        $this->assertSame('Nominal tidak sesuai', $c->fresh()->catatan_admin);

        $this->actingAs($this->admin)->post('/admin/pendaftaran/mahasiswa/bulk-terima', ['ids' => [$a->id, $b->id, $c->id]]);

        $this->assertSame(2, PendaftaranMahasiswa::where('status', 'diterima')->count());
        $this->assertTrue(AnggotaKelompok::where('nim', '20240510002')->exists());
        $this->assertFalse(AnggotaKelompok::where('nim', '20240510003')->exists());
    }

    public function test_dpl_can_register_and_admin_creates_account(): void
    {
        $this->post('/pendaftaran/dpl', $this->dataDpl())->assertRedirect('/pendaftaran/selesai');
        $p = PendaftaranDpl::first();
        Storage::disk('local')->assertExists($p->file_surat_kesanggupan);

        $this->post('/pendaftaran/dpl', $this->dataDpl())->assertSessionHasErrors('nip_nidn');
        $this->post('/pendaftaran/dpl', $this->dataDpl(['nip_nidn' => '999', 'surat_kesanggupan' => $this->pdf(2049)]))
            ->assertSessionHasErrors('surat_kesanggupan');

        $this->actingAs($this->admin)->get("/admin/pendaftaran/dpl/{$p->id}/file")->assertOk();

        $usernameBaru = PendaftaranService::usernameDplBerikutnya();
        $this->actingAs($this->admin)->post("/admin/pendaftaran/dpl/{$p->id}/terima")->assertSessionHas('success');

        $user = User::where('username', $usernameBaru)->first();
        $this->assertNotNull($user);
        $this->assertSame('dpl', $user->role);
        $this->assertSame('0412345678', $user->nip_nidn);
        $this->assertSame($user->id, $p->fresh()->user_id);

        // Dapat login dengan password default & tidak dipaksa ganti password
        $this->post('/logout');
        $this->post('/login', ['username' => $usernameBaru, 'password' => PendaftaranService::PASSWORD_DEFAULT_DPL])
            ->assertRedirect('/dpl/dashboard');
    }

    public function test_next_dpl_username_follows_existing_numbering(): void
    {
        User::create(['username' => 'DPL_PPL43', 'password' => 'x', 'role' => 'dpl', 'nama_lengkap' => 'X']);

        $this->assertSame('DPL_PPL44', PendaftaranService::usernameDplBerikutnya());
    }

    public function test_admin_can_close_registration_form(): void
    {
        $this->actingAs($this->admin)->post('/admin/pendaftaran/mahasiswa/toggle', ['dibuka' => '0'])->assertSessionHas('success');
        $this->post('/logout');

        $this->get('/pendaftaran/mahasiswa')->assertOk()->assertSee('sedang ditutup');
        $this->post('/pendaftaran/mahasiswa', $this->dataMahasiswa())->assertRedirect('/pendaftaran/mahasiswa');
        $this->assertSame(0, PendaftaranMahasiswa::count());

        // Formulir DPL tetap terbuka
        $this->get('/pendaftaran/dpl')->assertDontSee('Pendaftaran DPL sedang ditutup')->assertSee('Kirim Pendaftaran DPL');
    }

    public function test_non_admin_cannot_access_verification_or_files(): void
    {
        $this->post('/pendaftaran/mahasiswa', $this->dataMahasiswa());
        $p = PendaftaranMahasiswa::first();

        $this->get("/admin/pendaftaran/mahasiswa/{$p->id}/file")->assertRedirect('/login');

        $dpl = User::where('role', 'dpl')->first();
        $this->actingAs($dpl)->get("/admin/pendaftaran/mahasiswa/{$p->id}/file")->assertRedirect('/dpl/dashboard');
        $this->actingAs($dpl)->post("/admin/pendaftaran/mahasiswa/{$p->id}/terima")->assertRedirect('/dpl/dashboard');
        $this->assertSame('menunggu', $p->fresh()->status);
    }
}
