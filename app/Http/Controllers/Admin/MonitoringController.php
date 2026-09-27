<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KelompokPpl;
use App\Models\MonitoringDpl;
use App\Models\User;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    /**
     * Tampilkan Executive Dashboard & Rekapitulasi Monitoring Kunjungan DPL.
     */
    public function index(Request $request)
    {
        $dplList = User::where('role', 'dpl')->orderBy('nama_lengkap')->get();
        $kelompokList = KelompokPpl::orderBy('nama_kelompok')->get();

        $query = MonitoringDpl::with(['dpl', 'kelompok.mitra', 'kelompok.ketua']);

        if ($request->filled('dpl_id')) {
            $query->where('dpl_user_id', $request->dpl_id);
        }

        if ($request->filled('kelompok_id')) {
            $query->where('kelompok_id', $request->kelompok_id);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis_kunjungan', $request->jenis);
        }

        if ($request->filled('status')) {
            if ($request->status === 'disetujui') {
                $query->where('disetujui_kelompok', true);
            } elseif ($request->status === 'pending') {
                $query->where('disetujui_kelompok', false);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('dpl', function ($dq) use ($search) {
                    $dq->where('nama_lengkap', 'like', "%{$search}%");
                })->orWhereHas('kelompok', function ($kq) use ($search) {
                    $kq->where('nama_kelompok', 'like', "%{$search}%");
                })->orWhere('catatan_kunjungan', 'like', "%{$search}%");
            });
        }

        $monitoringList = $query->latest('tanggal_kunjungan')->paginate(20)->withQueryString();

        // Ringkasan Statistik Monitoring Seluruh DPL
        $totalKelompok = KelompokPpl::count();
        $totalKunjungan = MonitoringDpl::count();

        // Jumlah Kelompok yang sudah dikunjungi Penyerahan (Kunjungan 1)
        $penyerahanCompleteCount = KelompokPpl::whereHas('monitoringDpl', function ($q) {
            $q->where('jenis_kunjungan', 'penyerahan');
        })->count();

        // Jumlah Kelompok yang sudah dikunjungi Penarikan (Kunjungan 2)
        $penarikanCompleteCount = KelompokPpl::whereHas('monitoringDpl', function ($q) {
            $q->where('jenis_kunjungan', 'penarikan');
        })->count();

        $totalDisetujui = MonitoringDpl::where('disetujui_kelompok', true)->count();
        $totalPending = MonitoringDpl::where('disetujui_kelompok', false)->count();

        $statsSummary = [
            'total_kelompok' => $totalKelompok,
            'total_kunjungan' => $totalKunjungan,
            'penyerahan_complete' => $penyerahanCompleteCount,
            'penyerahan_pending' => max(0, $totalKelompok - $penyerahanCompleteCount),
            'penarikan_complete' => $penarikanCompleteCount,
            'penarikan_pending' => max(0, $totalKelompok - $penarikanCompleteCount),
            'total_disetujui' => $totalDisetujui,
            'total_pending' => $totalPending,
        ];

        return view('admin.monitoring.index', compact('monitoringList', 'dplList', 'kelompokList', 'statsSummary'));
    }
}
