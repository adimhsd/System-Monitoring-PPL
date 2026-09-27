<?php

namespace App\Http\Controllers\PicMitra;

use App\Http\Controllers\Controller;
use App\Models\KegiatanHarian;
use App\Models\KelompokPpl;
use App\Models\Mitra;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogbookController extends Controller
{
    /**
     * Tampilkan Daftar Logbook Kelompok Magang Penempatan Mitra.
     */
    public function index(Request $request)
    {
        $pic = Auth::user();
        $mitra = Mitra::where('pic_user_id', $pic->id)->first();

        if (! $mitra) {
            return redirect()->route('pic.dashboard')
                ->with('error', 'Akun Anda belum ditautkan dengan instansi Mitra PPL.');
        }

        $kelompok = KelompokPpl::where('mitra_id', $mitra->id)->first();

        if (! $kelompok) {
            return view('pic.logbook.index', [
                'kelompok' => null,
                'logbookList' => collect(),
            ]);
        }

        $query = KegiatanHarian::where('kelompok_id', $kelompok->id);

        if ($request->filled('status_dilihat')) {
            if ($request->status_dilihat === 'belum') {
                $query->where('dilihat_mitra', false);
            } elseif ($request->status_dilihat === 'sudah') {
                $query->where('dilihat_mitra', true);
            }
        }

        $logbookList = $query->orderBy('tanggal', 'desc')->paginate(15)->withQueryString();

        return view('pic.logbook.index', compact('kelompok', 'logbookList'));
    }

    /**
     * Detail Logbook Harian.
     */
    public function show(KegiatanHarian $logbook)
    {
        $pic = Auth::user();
        $mitra = Mitra::where('pic_user_id', $pic->id)->first();

        if (! $mitra || $logbook->kelompok->mitra_id !== $mitra->id) {
            abort(403, 'Akses ditolak. Logbook ini bukan lokasi magang mitra Anda.');
        }

        $logbook->load(['kelompok.dpl', 'kelompok.ketua', 'kelompok.anggota']);

        return view('pic.logbook.show', compact('logbook'));
    }

    /**
     * Tandai Logbook Telah Dilihat oleh PIC Mitra.
     */
    public function markAsViewed(Request $request, KegiatanHarian $logbook)
    {
        $pic = Auth::user();
        $mitra = Mitra::where('pic_user_id', $pic->id)->first();

        if (! $mitra || $logbook->kelompok->mitra_id !== $mitra->id) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'status_validasi_mitra' => 'required|in:sesuai,tidak_sesuai',
            'catatan_mitra' => 'nullable|string|max:1000',
        ]);

        $status = $request->input('status_validasi_mitra', 'sesuai');
        $catatan = $request->input('catatan_mitra');

        $logbook->update([
            'dilihat_mitra' => true,
            'dilihat_mitra_at' => now(),
            'status_validasi_mitra' => $status,
            'catatan_mitra' => $catatan,
        ]);

        $statusText = $status === 'sesuai' ? 'Sesuai' : 'Tidak Sesuai';

        // Kirim notifikasi ke Akun Kelompok
        NotifikasiService::kirim(
            $logbook->kelompok->ketua_user_id,
            'Logbook Di-Approve Pembimbing Mitra (' . $statusText . ')',
            'Logbook tanggal ' . $logbook->tanggal->format('d/m/Y') . ' telah di-approve oleh Pembimbing Mitra (' . $pic->nama_lengkap . ') dengan keterangan: ' . $statusText . '.',
            route('ketua.logbook.index')
        );

        return redirect()->back()
            ->with('success', 'Logbook berhasil di-approve dengan keterangan: ' . $statusText . '.');
    }
}
