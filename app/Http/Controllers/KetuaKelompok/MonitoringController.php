<?php

namespace App\Http\Controllers\KetuaKelompok;

use App\Http\Controllers\Controller;
use App\Models\KelompokPpl;
use App\Models\MonitoringDpl;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonitoringController extends Controller
{
    /**
     * Tampilkan Daftar Kunjungan Monitoring DPL & Status Persetujuan Kelompok.
     */
    public function index()
    {
        $ketua = Auth::user();
        $kelompok = KelompokPpl::where('ketua_user_id', $ketua->id)
            ->with(['dpl', 'mitra'])
            ->firstOrFail();

        $monitoringList = MonitoringDpl::with('dpl')
            ->where('kelompok_id', $kelompok->id)
            ->latest('tanggal_kunjungan')
            ->get();

        // Status Kunjungan Penyerahan (Kunjungan 1) & Penarikan (Kunjungan 2)
        $penyerahanRecord = $monitoringList->where('jenis_kunjungan', 'penyerahan')->first();
        $penarikanRecord = $monitoringList->where('jenis_kunjungan', 'penarikan')->first();

        return view('ketua.monitoring.index', compact('kelompok', 'monitoringList', 'penyerahanRecord', 'penarikanRecord'));
    }

    /**
     * Setujui / Konfirmasi Kunjungan DPL oleh Kelompok Mahasiswa.
     */
    public function approve(MonitoringDpl $monitoring)
    {
        $ketua = Auth::user();
        $kelompok = KelompokPpl::where('ketua_user_id', $ketua->id)->firstOrFail();

        if ($monitoring->kelompok_id !== $kelompok->id) {
            abort(403, 'Akses ditolak. Laporan kunjungan ini bukan untuk kelompok Anda.');
        }

        $monitoring->update([
            'disetujui_kelompok' => true,
            'tanggal_disetujui' => now(),
        ]);

        // Kirim Notifikasi ke DPL Pembimbing
        if ($monitoring->dpl_user_id) {
            $labelJenis = match($monitoring->jenis_kunjungan) {
                'penyerahan' => 'Kunjungan Awal (Penyerahan)',
                'penarikan' => 'Kunjungan Akhir (Penarikan)',
                default => 'Kunjungan Rutin',
            };

            Notifikasi::create([
                'user_id' => $monitoring->dpl_user_id,
                'judul' => 'Kunjungan DPL Disetujui Kelompok',
                'pesan' => "Kelompok {$kelompok->nama_kelompok} telah menyetujui bukti {$labelJenis} tanggal {$monitoring->tanggal_kunjungan->format('d/m/Y')}.",
                'link' => route('dpl.monitoring.index'),
                'is_read' => false,
            ]);
        }

        return redirect()->back()
            ->with('success', 'Kunjungan DPL berhasil dikonfirmasi dan disetujui oleh kelompok.');
    }
}
