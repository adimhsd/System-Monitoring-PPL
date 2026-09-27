<?php

namespace App\Http\Controllers\Dpl;

use App\Http\Controllers\Controller;
use App\Models\KegiatanHarian;
use App\Models\KelompokPpl;
use App\Services\PenilaianService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $dpl = Auth::user();

        $kelompokBimbingan = KelompokPpl::with(['mitra.picUser', 'ketua', 'anggota.penilaian', 'kegiatanHarian'])
            ->where('dpl_id', $dpl->id)
            ->get();

        $kelompokIds = $kelompokBimbingan->pluck('id');

        $totalKelompok = $kelompokBimbingan->count();
        $totalMahasiswa = $kelompokBimbingan->sum(fn ($k) => $k->anggota->count());

        // Logbook Approval Stats
        $pendingQuery = KegiatanHarian::whereIn('kelompok_id', $kelompokIds)->where('dilihat_dpl', false);
        $totalPendingApproval = (clone $pendingQuery)->count();
        $totalApprovedDpl = KegiatanHarian::whereIn('kelompok_id', $kelompokIds)->where('dilihat_dpl', true)->count();
        $totalLogbookSubmitted = KegiatanHarian::whereIn('kelompok_id', $kelompokIds)->count();

        // Penilaian DPL Progress: kelompok yang seluruh anggotanya sudah diberi Nilai Laporan DPL
        $penilaianDoneCount = $kelompokBimbingan
            ->filter(fn ($k) => PenilaianService::kelompokSelesai($k, 'nilai_dpl'))
            ->count();

        // Recent 5 Pending Logbooks needing approval
        $recentPendingLogbooks = (clone $pendingQuery)
            ->with(['kelompok.ketua', 'kelompok.mitra'])
            ->orderBy('tanggal', 'desc')
            ->take(5)
            ->get();

        return view('dpl.dashboard', compact(
            'dpl',
            'kelompokBimbingan',
            'totalKelompok',
            'totalMahasiswa',
            'totalPendingApproval',
            'totalApprovedDpl',
            'totalLogbookSubmitted',
            'penilaianDoneCount',
            'recentPendingLogbooks'
        ));
    }
}
