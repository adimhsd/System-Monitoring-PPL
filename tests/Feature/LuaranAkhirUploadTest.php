<?php

namespace Tests\Feature;

use App\Models\KelompokPpl;
use App\Models\LuaranKelompok;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\Traits\CreatesTestPplData;

class LuaranAkhirUploadTest extends TestCase
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

    public function test_ketua_can_access_luaran_index_page(): void
    {
        $response = $this->actingAs($this->ketuaUser)->get('/ketua/luaran');
        $response->assertStatus(200);
        $response->assertSee('Unggah Luaran Akhir PPL');
    }

    public function test_ketua_can_upload_pdf_report_and_youtube_link(): void
    {
        $pdf = UploadedFile::fake()->create('laporan_akhir.pdf', 2000, 'application/pdf'); // 2MB PDF

        $response = $this->actingAs($this->ketuaUser)->post('/ketua/luaran', [
            'file_laporan_pdf' => $pdf,
            'url_video' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);

        $response->assertRedirect('/ketua/luaran');

        $this->assertDatabaseHas('luaran_kelompok', [
            'kelompok_id' => $this->kelompok->id,
            'url_video' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);

        // Verify notification dispatched to DPL
        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $this->kelompok->dpl_id,
            'judul' => 'Luaran Akhir PPL Diunggah',
        ]);
    }

    public function test_upload_rejects_invalid_file_format(): void
    {
        $doc = UploadedFile::fake()->create('laporan.docx', 500, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        $response = $this->actingAs($this->ketuaUser)->post('/ketua/luaran', [
            'file_laporan_pdf' => $doc,
            'url_video' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);

        $response->assertSessionHasErrors('file_laporan_pdf');
    }

    public function test_upload_rejects_non_youtube_video_url(): void
    {
        $pdf = UploadedFile::fake()->create('laporan.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->ketuaUser)->post('/ketua/luaran', [
            'file_laporan_pdf' => $pdf,
            'url_video' => 'https://vimeo.com/12345678',
        ]);

        $response->assertSessionHasErrors('url_video');
    }

    public function test_authorized_user_can_download_pdf_report(): void
    {
        $luaran = LuaranKelompok::create([
            'kelompok_id' => $this->kelompok->id,
            'file_laporan_pdf' => 'luaran/kelompok_1/laporan_test.pdf',
            'url_video' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'uploaded_at' => now(),
        ]);

        Storage::disk('local')->put($luaran->file_laporan_pdf, '%PDF-1.4 dummy content');

        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get("/luaran/pdf/{$luaran->id}");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
