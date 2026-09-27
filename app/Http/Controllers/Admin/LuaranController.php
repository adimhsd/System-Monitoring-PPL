<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KelompokPpl;
use App\Models\LuaranKelompok;
use Illuminate\Http\Request;

class LuaranController extends Controller
{
    /**
     * Overview Luaran Akhir Seluruh Kelompok PPL & Ringkasan Statistik.
     */
    public function index(Request $request)
    {
        $query = KelompokPpl::with(['mitra', 'dpl', 'ketua', 'luaran']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_kelompok', 'like', "%{$search}%")
                  ->orWhereHas('mitra', function ($m) use ($search) {
                      $m->where('nama_mitra', 'like', "%{$search}%");
                  })
                  ->orWhereHas('dpl', function ($d) use ($search) {
                      $d->where('nama_lengkap', 'like', "%{$search}%");
                  })
                  ->orWhereHas('ketua', function ($k) use ($search) {
                      $k->where('nama_lengkap', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'lengkap') {
                $query->whereHas('luaran', function ($q) {
                    $q->whereNotNull('file_laporan_pdf')->whereNotNull('url_video');
                });
            } elseif ($request->status === 'parsial') {
                $query->whereHas('luaran', function ($q) {
                    $q->where(function ($sub) {
                        $sub->whereNull('file_laporan_pdf')->orWhereNull('url_video');
                    })->where(function ($sub) {
                        $sub->whereNotNull('file_laporan_pdf')->orWhereNotNull('url_video');
                    });
                });
            } elseif ($request->status === 'belum') {
                $query->whereDoesntHave('luaran');
            }
        }

        $kelompokList = $query->latest()->paginate(20)->withQueryString();

        // Calculations for Summary Stat Cards
        $totalKelompok = KelompokPpl::count();
        $luaranList = LuaranKelompok::all();

        $kelompokLengkap = $luaranList->filter(function ($l) {
            return !empty($l->file_laporan_pdf) && !empty($l->url_video);
        })->count();

        $kelompokParsial = $luaranList->filter(function ($l) {
            return (!empty($l->file_laporan_pdf) && empty($l->url_video))
                || (empty($l->file_laporan_pdf) && !empty($l->url_video));
        })->count();

        $kelompokBelum = max(0, $totalKelompok - ($kelompokLengkap + $kelompokParsial));

        $pdfTerkumpul = $luaranList->filter(function ($l) {
            return !empty($l->file_laporan_pdf);
        })->count();
        $pdfBelum = max(0, $totalKelompok - $pdfTerkumpul);

        $videoTerkumpul = $luaranList->filter(function ($l) {
            return !empty($l->url_video);
        })->count();
        $videoBelum = max(0, $totalKelompok - $videoTerkumpul);

        $persentaseProgres = $totalKelompok > 0
            ? round(($kelompokLengkap / $totalKelompok) * 100, 1)
            : 0;

        $statsSummary = [
            'total_kelompok' => $totalKelompok,
            'kelompok_lengkap' => $kelompokLengkap,
            'kelompok_parsial' => $kelompokParsial,
            'kelompok_belum' => $kelompokBelum,
            'pdf_terkumpul' => $pdfTerkumpul,
            'pdf_belum' => $pdfBelum,
            'video_terkumpul' => $videoTerkumpul,
            'video_belum' => $videoBelum,
            'persentase_progres' => $persentaseProgres,
        ];

        return view('admin.luaran.index', compact('kelompokList', 'statsSummary'));
    }
}
