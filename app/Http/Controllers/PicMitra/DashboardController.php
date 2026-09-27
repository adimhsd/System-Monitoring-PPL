<?php

namespace App\Http\Controllers\PicMitra;

use App\Http\Controllers\Controller;
use App\Models\KegiatanHarian;
use App\Models\KelompokPpl;
use App\Models\Mitra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $pic = Auth::user();
        $mitra = Mitra::where('pic_user_id', $pic->id)->first();
        
        $kelompok = $mitra ? KelompokPpl::with(['ketua', 'anggota', 'dpl', 'penilaian'])
            ->where('mitra_id', $mitra->id)
            ->first() : null;

        if (! $kelompok) {
            return view('pic.dashboard', compact('pic', 'mitra', 'kelompok'));
        }

        // Summary stats for PIC Mitra
        $logbookQuery = KegiatanHarian::where('kelompok_id', $kelompok->id);
        $totalLogbook = (clone $logbookQuery)->count();
        $pendingMitraApprovalCount = (clone $logbookQuery)->where('dilihat_mitra', false)->count();
        $approvedMitraCount = (clone $logbookQuery)->where('dilihat_mitra', true)->count();

        // Penilaian Status (60%)
        $penilaianMitraDone = $kelompok->penilaian && $kelompok->penilaian->mitra_nilai_total !== null;

        // Recent 5 pending logbooks needing PIC Mitra approval
        $recentPendingLogbooks = (clone $logbookQuery)
            ->where('dilihat_mitra', false)
            ->orderBy('tanggal', 'desc')
            ->take(5)
            ->get();

        return view('pic.dashboard', compact(
            'pic',
            'mitra',
            'kelompok',
            'totalLogbook',
            'pendingMitraApprovalCount',
            'approvedMitraCount',
            'penilaianMitraDone',
            'recentPendingLogbooks'
        ));
    }
}
