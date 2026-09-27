<?php

namespace App\Http\Controllers;

use App\Models\LuaranKelompok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LuaranFileController extends Controller
{
    /**
     * Stream / Download Secure PDF Laporan Akhir PPL.
     */
    public function download(Request $request, LuaranKelompok $luaran)
    {
        $user = Auth::user();
        $kelompok = $luaran->kelompok;

        // Authorization check: Admin, DPL, PIC Mitra, or Ketua Kelompok
        $isAuthorized = match ($user->role) {
            'admin' => true,
            'dpl' => $kelompok->dpl_id === $user->id,
            'pic_mitra' => $kelompok->mitra && $kelompok->mitra->pic_user_id === $user->id,
            'ketua_kelompok' => $kelompok->ketua_user_id === $user->id,
            default => false,
        };

        if (! $isAuthorized) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengunduh laporan PDF ini.');
        }

        $filePath = $luaran->file_laporan_pdf;
        $disk = config('filesystems.disks.r2.key') ? 'r2' : 'local';

        if (! Storage::disk($disk)->exists($filePath)) {
            if (Storage::disk('local')->exists($filePath)) {
                $disk = 'local';
            } else {
                abort(404, 'File laporan PDF tidak ditemukan.');
            }
        }

        $fileContent = Storage::disk($disk)->get($filePath);
        $fileName = 'Laporan_PPL_' . \Illuminate\Support\Str::slug($kelompok->nama_kelompok) . '.pdf';

        return response($fileContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            'Cache-Control' => 'private, max-age=1800',
        ]);
    }
}
