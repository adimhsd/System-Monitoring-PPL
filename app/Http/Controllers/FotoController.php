<?php

namespace App\Http\Controllers;

use App\Models\KegiatanHarian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FotoController extends Controller
{
    /**
     * Tampilkan / Stream Foto Dokumentasi Logbook dengan Proteksi Hak Akses & Temporary Signed URL.
     */
    public function show(Request $request, KegiatanHarian $kegiatan)
    {
        $user = Auth::user();
        $kelompok = $kegiatan->kelompok;

        // Security Check: Memastikan hanya user berwenang yang bisa melihat foto
        $isAuthorized = match ($user->role) {
            'admin' => true,
            'dpl' => $kelompok->dpl_id === $user->id,
            'pic_mitra' => $kelompok->mitra && $kelompok->mitra->pic_user_id === $user->id,
            'ketua_kelompok' => $kelompok->ketua_user_id === $user->id,
            default => false,
        };

        if (! $isAuthorized) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat foto dokumentasi ini.');
        }

        $filePath = $kegiatan->foto_dokumentasi;

        // Jika foto belum selesai dikompresi oleh queue
        if (str_starts_with($filePath, 'temp/')) {
            $disk = 'local';
        } else {
            $disk = config('filesystems.disks.r2.key') ? 'r2' : 'local';
        }

        if (! Storage::disk($disk)->exists($filePath)) {
            // Coba cek disk local jika di r2 tidak ketemu
            if (Storage::disk('local')->exists($filePath)) {
                $disk = 'local';
            } else {
                abort(404, 'File foto dokumentasi tidak ditemukan.');
            }
        }

        // Stream file foto secara aman
        $fileStream = Storage::disk($disk)->get($filePath);
        $mimeType = Storage::disk($disk)->mimeType($filePath) ?? 'image/webp';

        return response($fileStream, 200, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'private, max-age=900', // Cache 15 menit
        ]);
    }
}
