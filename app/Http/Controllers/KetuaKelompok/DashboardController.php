<?php

namespace App\Http\Controllers\KetuaKelompok;

use App\Http\Controllers\Controller;
use App\Models\KegiatanHarian;
use App\Models\KelompokPpl;
use App\Models\LuaranKelompok;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $ketua = Auth::user();
        
        $kelompok = KelompokPpl::with(['mitra.picUser', 'dpl', 'anggota', 'luaran'])
            ->where('ketua_user_id', $ketua->id)
            ->orWhereHas('anggota', function ($q) use ($ketua) {
                // If member user is logged in
                $q->where('nim', $ketua->username);
            })
            ->first();

        if (! $kelompok) {
            return view('ketua.dashboard', [
                'ketua' => $ketua,
                'kelompok' => null,
            ]);
        }

        // Summary Stats
        $logbookQuery = KegiatanHarian::where('kelompok_id', $kelompok->id);
        $totalLogbook = (clone $logbookQuery)->count();
        $approvedMitraCount = (clone $logbookQuery)->where('dilihat_mitra', true)->count();
        $approvedDplCount = (clone $logbookQuery)->where('dilihat_dpl', true)->count();
        
        // Logbook Hari Ini
        $todayLogbook = (clone $logbookQuery)->whereDate('tanggal', now()->toDateString())->first();

        // Recent 5 Logbooks
        $recentLogbooks = (clone $logbookQuery)->orderBy('tanggal', 'desc')->take(5)->get();

        // Luaran Akhir Status
        $luaran = $kelompok->luaran;

        return view('ketua.dashboard', compact(
            'ketua',
            'kelompok',
            'totalLogbook',
            'approvedMitraCount',
            'approvedDplCount',
            'todayLogbook',
            'recentLogbooks',
            'luaran'
        ));
    }
}
