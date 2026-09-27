<?php

namespace App\Http\Controllers\Dpl;

use App\Http\Controllers\Controller;
use App\Models\KegiatanHarian;
use App\Models\KelompokPpl;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogbookController extends Controller
{
    /**
     * Tampilkan Daftar Logbook Kelompok Bimbingan DPL.
     */
    public function index(Request $request)
    {
        $dpl = Auth::user();
        
        $kelompokIds = KelompokPpl::where('dpl_id', $dpl->id)->pluck('id');

        $query = KegiatanHarian::with(['kelompok.mitra', 'kelompok.ketua'])
            ->whereIn('kelompok_id', $kelompokIds);

        if ($request->filled('kelompok_id')) {
            $query->where('kelompok_id', $request->kelompok_id);
        }

        if ($request->filled('status_dilihat')) {
            if ($request->status_dilihat === 'belum') {
                $query->where('dilihat_dpl', false);
            } elseif ($request->status_dilihat === 'sudah') {
                $query->where('dilihat_dpl', true);
            }
        }

        $logbookList = $query->orderBy('tanggal', 'desc')->paginate(15)->withQueryString();

        $kelompokList = KelompokPpl::where('dpl_id', $dpl->id)->get();

        return view('dpl.logbook.index', compact('logbookList', 'kelompokList'));
    }

    /**
     * Detail Logbook Harian.
     */
    public function show(KegiatanHarian $logbook)
    {
        $dpl = Auth::user();
        
        if ($logbook->kelompok->dpl_id !== $dpl->id) {
            abort(403, 'Akses ditolak. Logbook ini bukan kelompok bimbingan Anda.');
        }

        $logbook->load(['kelompok.mitra', 'kelompok.ketua', 'kelompok.anggota']);

        return view('dpl.logbook.show', compact('logbook'));
    }

    /**
     * Tandai Logbook Telah Dilihat oleh DPL.
     */
    public function markAsViewed(KegiatanHarian $logbook)
    {
        $dpl = Auth::user();

        if ($logbook->kelompok->dpl_id !== $dpl->id) {
            abort(403, 'Akses ditolak.');
        }

        if (! $logbook->dilihat_dpl) {
            $logbook->update([
                'dilihat_dpl' => true,
                'dilihat_dpl_at' => now(),
            ]);

            // Kirim notifikasi ke Ketua Kelompok
            NotifikasiService::kirim(
                $logbook->kelompok->ketua_user_id,
                'Logbook Di-Approve DPL',
                'Logbook tanggal ' . $logbook->tanggal->format('d/m/Y') . ' telah di-approve oleh Dosen Pembimbing Lapangan (' . $dpl->nama_lengkap . ').',
                route('ketua.logbook.index')
            );
        }

        return redirect()->back()
            ->with('success', 'Logbook berhasil di-approve.');
    }
}
