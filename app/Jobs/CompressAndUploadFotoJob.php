<?php

namespace App\Jobs;

use App\Models\KegiatanHarian;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class CompressAndUploadFotoJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $backoff = 10;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $kegiatanHarianId,
        public string $tempFilePath
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $kegiatan = KegiatanHarian::find($this->kegiatanHarianId);

        if (! $kegiatan) {
            Log::warning("CompressAndUploadFotoJob: KegiatanHarian ID {$this->kegiatanHarianId} tidak ditemukan.");
            return;
        }

        $fullTempPath = storage_path('app/private/' . $this->tempFilePath);

        if (! file_exists($fullTempPath)) {
            $fullTempPath = storage_path('app/' . $this->tempFilePath);
        }

        if (! file_exists($fullTempPath)) {
            Log::error("CompressAndUploadFotoJob: Temp file {$this->tempFilePath} tidak ditemukan.");
            return;
        }

        try {
            // Read, Resize (max width 800px), and Convert to WebP via Intervention Image v3
            $manager = new ImageManager(new Driver());
            $image = $manager->read($fullTempPath);
            
            if ($image->width() > 800) {
                $image->scale(width: 800);
            }

            $webpData = (string) $image->toWebp(quality: 80);

            // Path simpan di R2 / Storage: logbooks/kelompok_{id}/logbook_{tanggal}_{uniqid}.webp
            $fileName = 'logbooks/kelompok_' . $kegiatan->kelompok_id . '/logbook_' . $kegiatan->tanggal->format('Y-m-d') . '_' . uniqid() . '.webp';

            // Coba simpan ke R2 private bucket, jika credentials R2 belum ada, fallback ke disk local private
            $targetDisk = config('filesystems.disks.r2.key') ? 'r2' : 'local';
            
            Storage::disk($targetDisk)->put($fileName, $webpData, [
                'visibility' => 'private',
            ]);

            // Update record kegiatan_harian dengan path foto final
            $kegiatan->update([
                'foto_dokumentasi' => $fileName,
            ]);

            // Hapus file temporary lokal
            @unlink($fullTempPath);

            Log::info("CompressAndUploadFotoJob: Berhasil kompresi & upload foto logbook ID {$kegiatan->id} ke disk {$targetDisk}.");
        } catch (\Throwable $e) {
            Log::error("CompressAndUploadFotoJob Error: " . $e->getMessage());
            throw $e;
        }
    }
}
