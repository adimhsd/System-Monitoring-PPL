<?php

namespace App\Http\Controllers;

use App\Models\KegiatanHarian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LogbookFotoController extends Controller
{
    /**
     * Tampilkan atau download foto dokumentasi logbook.
     */
    public function show(KegiatanHarian $logbook)
    {
        if (! $logbook->foto_dokumentasi) {
            abort(404, 'Foto dokumentasi tidak ditemukan.');
        }

        $disk = config('filesystems.disks.r2.key') ? 'r2' : 'local';

        if (Storage::disk($disk)->exists($logbook->foto_dokumentasi)) {
            return Storage::disk($disk)->response($logbook->foto_dokumentasi);
        }

        if (Storage::disk('local')->exists($logbook->foto_dokumentasi)) {
            return Storage::disk('local')->response($logbook->foto_dokumentasi);
        }

        abort(404, 'File foto tidak ditemukan di storage.');
    }
}
