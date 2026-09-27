<?php

namespace App\Http\Controllers\Dpl;

use App\Http\Controllers\Controller;
use App\Models\KelompokPpl;
use App\Models\MonitoringDpl;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MonitoringController extends Controller
{
    /**
     * Tampilkan Dashboard & Daftar Laporan Kunjungan Monitoring DPL.
     */
    public function index(Request $request)
    {
        $dpl = Auth::user();

        // Ambil daftar kelompok bimbingan DPL
        $kelompokList = KelompokPpl::where('dpl_id', $dpl->id)->get();
        $kelompokIds = $kelompokList->pluck('id')->toArray();

        $query = MonitoringDpl::with(['kelompok.mitra', 'kelompok.ketua'])
            ->where('dpl_user_id', $dpl->id);

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

        $monitoringList = $query->latest('tanggal_kunjungan')->paginate(20)->withQueryString();

        // Hitung Ringkasan Statistik
        $totalKunjungan = MonitoringDpl::where('dpl_user_id', $dpl->id)->count();

        // Kunjungan 1 (Penyerahan) completed groups count
        $penyerahanDoneCount = KelompokPpl::where('dpl_id', $dpl->id)
            ->whereHas('monitoringDpl', function ($q) {
                $q->where('jenis_kunjungan', 'penyerahan');
            })->count();

        // Kunjungan 2 (Penarikan) completed groups count
        $penarikanDoneCount = KelompokPpl::where('dpl_id', $dpl->id)
            ->whereHas('monitoringDpl', function ($q) {
                $q->where('jenis_kunjungan', 'penarikan');
            })->count();

        $totalDisetujui = MonitoringDpl::where('dpl_user_id', $dpl->id)->where('disetujui_kelompok', true)->count();
        $totalPending = MonitoringDpl::where('dpl_user_id', $dpl->id)->where('disetujui_kelompok', false)->count();

        $statsSummary = [
            'total_kelompok' => count($kelompokList),
            'total_kunjungan' => $totalKunjungan,
            'penyerahan_done' => $penyerahanDoneCount,
            'penarikan_done' => $penarikanDoneCount,
            'total_disetujui' => $totalDisetujui,
            'total_pending' => $totalPending,
        ];

        return view('dpl.monitoring.index', compact('monitoringList', 'kelompokList', 'statsSummary'));
    }

    /**
     * Form Tambah Laporan Kunjungan Monitoring DPL.
     */
    public function create()
    {
        $dpl = Auth::user();
        $kelompokList = KelompokPpl::where('dpl_id', $dpl->id)->with('mitra')->orderBy('nama_kelompok')->get();

        if ($kelompokList->isEmpty()) {
            return redirect()->route('dpl.monitoring.index')
                ->with('error', 'Anda belum memiliki kelompok bimbingan yang diplotkan.');
        }

        return view('dpl.monitoring.create', compact('kelompokList'));
    }

    /**
     * Simpan Laporan Kunjungan Monitoring DPL Baru.
     */
    public function store(Request $request)
    {
        $dpl = Auth::user();
        $assignedKelompokIds = KelompokPpl::where('dpl_id', $dpl->id)->pluck('id')->toArray();

        $request->validate([
            'kelompok_id' => ['required', Rule::in($assignedKelompokIds)],
            'jenis_kunjungan' => ['required', Rule::in(['penyerahan', 'penarikan', 'kunjungan_rutin'])],
            'tanggal_kunjungan' => ['required', 'date'],
            'waktu_kunjungan' => ['nullable', 'date_format:H:i'],
            'catatan_kunjungan' => ['required', 'string', 'min:10'],
            'foto_kunjungan' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'kelompok_id.required' => 'Pilih kelompok PPL yang dikunjungi.',
            'kelompok_id.in' => 'Kelompok PPL tidak valid atau bukan bimbingan Anda.',
            'jenis_kunjungan.required' => 'Jenis kunjungan wajib dipilih.',
            'tanggal_kunjungan.required' => 'Tanggal kunjungan wajib diisi.',
            'catatan_kunjungan.required' => 'Catatan / hasil evaluasi kunjungan wajib diisi.',
            'catatan_kunjungan.min' => 'Catatan kunjungan minimal 10 karakter.',
            'foto_kunjungan.required' => 'Foto dokumentasi kunjungan wajib diunggah.',
            'foto_kunjungan.image' => 'File foto harus berupa gambar (JPG, JPEG, PNG, WebP).',
            'foto_kunjungan.max' => 'Ukuran file foto maksimal 5MB.',
        ]);

        $fotoPath = $request->file('foto_kunjungan')->store('monitoring_dpl', 'public');

        $monitoring = MonitoringDpl::create([
            'dpl_user_id' => $dpl->id,
            'kelompok_id' => $request->kelompok_id,
            'jenis_kunjungan' => $request->jenis_kunjungan,
            'tanggal_kunjungan' => $request->tanggal_kunjungan,
            'waktu_kunjungan' => $request->waktu_kunjungan,
            'catatan_kunjungan' => $request->catatan_kunjungan,
            'foto_kunjungan' => $fotoPath,
            'disetujui_kelompok' => false,
        ]);

        // Kirim Notifikasi ke Ketua Kelompok Mahasiswa
        $kelompok = KelompokPpl::find($request->kelompok_id);
        if ($kelompok && $kelompok->ketua_user_id) {
            $labelJenis = match($request->jenis_kunjungan) {
                'penyerahan' => 'Kunjungan Awal (Penyerahan)',
                'penarikan' => 'Kunjungan Akhir (Penarikan)',
                default => 'Kunjungan Rutin',
            };

            Notifikasi::create([
                'user_id' => $kelompok->ketua_user_id,
                'judul' => 'Laporan Kunjungan DPL Baru',
                'pesan' => "DPL {$dpl->nama_lengkap} telah mengunggah bukti {$labelJenis} pada tanggal {$request->tanggal_kunjungan}. Mohon lakukan verifikasi & persetujuan di sistem.",
                'link' => route('student.monitoring.index'),
                'is_read' => false,
            ]);
        }

        return redirect()->route('dpl.monitoring.index')
            ->with('success', 'Laporan kunjungan monitoring DPL berhasil disimpan. Kelompok mahasiswa dapat melakukan verifikasi.');
    }

    /**
     * Form Edit Laporan Kunjungan Monitoring DPL.
     */
    public function edit(MonitoringDpl $monitoring)
    {
        $dpl = Auth::user();

        if ($monitoring->dpl_user_id !== $dpl->id) {
            abort(403, 'Akses ditolak. Laporan kunjungan ini bukan milik Anda.');
        }

        $kelompokList = KelompokPpl::where('dpl_id', $dpl->id)->with('mitra')->orderBy('nama_kelompok')->get();

        return view('dpl.monitoring.edit', compact('monitoring', 'kelompokList'));
    }

    /**
     * Update Laporan Kunjungan Monitoring DPL.
     */
    public function update(Request $request, MonitoringDpl $monitoring)
    {
        $dpl = Auth::user();

        if ($monitoring->dpl_user_id !== $dpl->id) {
            abort(403, 'Akses ditolak. Laporan kunjungan ini bukan milik Anda.');
        }

        $assignedKelompokIds = KelompokPpl::where('dpl_id', $dpl->id)->pluck('id')->toArray();

        $request->validate([
            'kelompok_id' => ['required', Rule::in($assignedKelompokIds)],
            'jenis_kunjungan' => ['required', Rule::in(['penyerahan', 'penarikan', 'kunjungan_rutin'])],
            'tanggal_kunjungan' => ['required', 'date'],
            'waktu_kunjungan' => ['nullable', 'date_format:H:i'],
            'catatan_kunjungan' => ['required', 'string', 'min:10'],
            'foto_kunjungan' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $payload = [
            'kelompok_id' => $request->kelompok_id,
            'jenis_kunjungan' => $request->jenis_kunjungan,
            'tanggal_kunjungan' => $request->tanggal_kunjungan,
            'waktu_kunjungan' => $request->waktu_kunjungan,
            'catatan_kunjungan' => $request->catatan_kunjungan,
        ];

        if ($request->hasFile('foto_kunjungan')) {
            if ($monitoring->foto_kunjungan && Storage::disk('public')->exists($monitoring->foto_kunjungan)) {
                Storage::disk('public')->delete($monitoring->foto_kunjungan);
            }
            $payload['foto_kunjungan'] = $request->file('foto_kunjungan')->store('monitoring_dpl', 'public');
        }

        $monitoring->update($payload);

        return redirect()->route('dpl.monitoring.index')
            ->with('success', 'Laporan kunjungan monitoring DPL berhasil diperbarui.');
    }

    /**
     * Hapus Laporan Kunjungan Monitoring DPL.
     */
    public function destroy(MonitoringDpl $monitoring)
    {
        $dpl = Auth::user();

        if ($monitoring->dpl_user_id !== $dpl->id) {
            abort(403, 'Akses ditolak. Laporan kunjungan ini bukan milik Anda.');
        }

        if ($monitoring->foto_kunjungan && Storage::disk('public')->exists($monitoring->foto_kunjungan)) {
            Storage::disk('public')->delete($monitoring->foto_kunjungan);
        }

        $monitoring->delete();

        return redirect()->route('dpl.monitoring.index')
            ->with('success', 'Laporan kunjungan monitoring DPL berhasil dihapus.');
    }
}
