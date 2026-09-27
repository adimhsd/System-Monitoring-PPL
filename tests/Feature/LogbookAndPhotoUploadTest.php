<?php

namespace Tests\Feature;

use App\Jobs\CompressAndUploadFotoJob;
use App\Models\KegiatanHarian;
use App\Models\KelompokPpl;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;

use Tests\TestCase;
use Tests\Traits\CreatesTestPplData;

class LogbookAndPhotoUploadTest extends TestCase
{
    use RefreshDatabase, CreatesTestPplData;

    protected User $ketuaUser;
    protected KelompokPpl $kelompok;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->createTestPplData();

        $this->ketuaUser = $this->ketuaUser;
        $this->kelompok = $this->testKelompok;
    }

    public function test_ketua_can_access_logbook_create_page(): void
    {
        $response = $this->actingAs($this->ketuaUser)->get('/ketua/logbook/create');
        $response->assertStatus(200);
        $response->assertSee('Input Logbook Harian PPL');
    }

    public function test_ketua_can_store_logbook_with_photo_upload_and_dispatch_job(): void
    {
        Queue::fake();

        $photo = UploadedFile::fake()->image('dokumentasi.jpg', 1200, 1200)->size(500); // 500KB

        $response = $this->actingAs($this->ketuaUser)->post('/ketua/logbook', [
            'tanggal' => date('Y-m-d'),
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '16:00',
            'deskripsi_kegiatan' => 'Kegiatan penginputan data keuangan perusahaan hari ini.',
            'foto_dokumentasi' => $photo,
        ]);

        $response->assertRedirect('/ketua/logbook');

        $this->assertDatabaseHas('kegiatan_harian', [
            'kelompok_id' => $this->kelompok->id,
            'deskripsi_kegiatan' => 'Kegiatan penginputan data keuangan perusahaan hari ini.',
        ]);

        Queue::assertPushed(CompressAndUploadFotoJob::class);
    }

    public function test_logbook_backdated_more_than_one_day_is_flagged_terlambat(): void
    {
        Queue::fake();

        $photo = UploadedFile::fake()->image('dokumentasi_old.png', 800, 600)->size(300);
        $backdate = date('Y-m-d', strtotime('-3 days'));

        $response = $this->actingAs($this->ketuaUser)->post('/ketua/logbook', [
            'tanggal' => $backdate,
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '16:00',
            'deskripsi_kegiatan' => 'Input logbook terlambat tiga hari yang lalu.',
            'foto_dokumentasi' => $photo,
        ]);

        $response->assertRedirect('/ketua/logbook');

        $this->assertDatabaseHas('kegiatan_harian', [
            'kelompok_id' => $this->kelompok->id,
            'deskripsi_kegiatan' => 'Input logbook terlambat tiga hari yang lalu.',
        ]);
    }

    public function test_unique_constraint_prevents_duplicate_logbook_on_same_date(): void
    {
        $existing = KegiatanHarian::create([
            'kelompok_id' => $this->kelompok->id,
            'tanggal' => '2026-05-10',
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '16:00',
            'deskripsi_kegiatan' => 'Kegiatan 1',
            'foto_dokumentasi' => 'temp/test.jpg',
        ]);

        $photo = UploadedFile::fake()->image('dup.jpg')->size(200);

        $response = $this->actingAs($this->ketuaUser)->post('/ketua/logbook', [
            'tanggal' => '2026-05-10',
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '16:00',
            'deskripsi_kegiatan' => 'Kegiatan duplikat',
            'foto_dokumentasi' => $photo,
        ]);

        // Should redirect to edit page with info message
        $response->assertRedirect("/ketua/logbook/{$existing->id}/edit");
    }

    public function test_authorized_user_can_view_logbook_photo(): void
    {
        $kegiatan = KegiatanHarian::first();
        \Illuminate\Support\Facades\Storage::disk('local')->put($kegiatan->foto_dokumentasi, 'fake-image-content');

        // Admin should be authorized
        $admin = User::where('role', 'admin')->first();
        $response = $this->actingAs($admin)->get("/foto/{$kegiatan->id}");
        $response->assertStatus(200);
    }
}
