<?php

namespace App\Http\Controllers\Dpl;

use App\Http\Controllers\Controller;
use App\Models\AnggotaKelompok;
use App\Models\KelompokPpl;
use App\Models\PenilaianPpl;
use App\Services\PenilaianService;
use App\Services\RekapNilaiExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenilaianController extends Controller
{
    private const FILTERS = ['search', 'prodi', 'kelompok_id', 'status', 'huruf'];

    /**
     * Input & Rekap Nilai mahasiswa bimbingan DPL.
     */
    public function index(Request $request)
    {
        $dpl = Auth::user();
        $filters = $request->only(self::FILTERS);

        $mahasiswaList = PenilaianService::queryMahasiswa($dpl, $filters)
            ->orderBy('kelompok_id')
            ->orderBy('nama')
            ->paginate(25)
            ->withQueryString();

        $statsSummary = PenilaianService::statistik(PenilaianService::queryMahasiswa($dpl));

        $kelompokList = KelompokPpl::where('dpl_id', $dpl->id)->orderBy('nama_kelompok')->get(['id', 'nama_kelompok']);

        $kelompokTerpilih = ! empty($filters['kelompok_id'])
            ? KelompokPpl::with(['mitra', 'luaran'])->where('dpl_id', $dpl->id)->find($filters['kelompok_id'])
            : null;

        $skalaHuruf = PenilaianPpl::skalaNilaiHuruf();

        return view('dpl.penilaian.index', compact(
            'mahasiswaList', 'kelompokList', 'kelompokTerpilih', 'skalaHuruf', 'statsSummary'
        ));
    }

    /**
     * Tautan lama per kelompok diarahkan ke daftar nilai yang terfilter.
     */
    public function edit(KelompokPpl $kelompok)
    {
        $this->pastikanBimbingan($kelompok->dpl_id);

        return redirect()->route('dpl.penilaian.index', ['kelompok_id' => $kelompok->id]);
    }

    /**
     * Simpan Nilai Laporan DPL (Bobot 40%).
     */
    public function update(Request $request, AnggotaKelompok $mahasiswa)
    {
        $this->pastikanBimbingan($mahasiswa->kelompok?->dpl_id);

        $data = $request->validate([
            'nilai_dpl' => ['required', 'numeric', 'min:0', 'max:100'],
            'catatan_dpl' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:draft,locked'],
        ], [
            'nilai_dpl.required' => 'Nilai Laporan DPL wajib diisi (0-100).',
        ]);

        $p = PenilaianService::simpan($mahasiswa, $data, Auth::user());

        $pesan = $p->nilai_akhir !== null
            ? "Nilai {$mahasiswa->nama} berhasil diperbarui (Akhir: " . number_format($p->nilai_akhir, 2) . " / Grade: {$p->nilai_huruf})."
            : "Nilai Laporan DPL {$mahasiswa->nama} tersimpan. Menunggu Nilai Mitra dari PIC Mitra.";

        return back()->with('success', $pesan);
    }

    public function lock(AnggotaKelompok $mahasiswa)
    {
        $this->pastikanBimbingan($mahasiswa->kelompok?->dpl_id);

        if (! PenilaianService::kunci($mahasiswa, Auth::user())) {
            return back()->with('error', "Nilai {$mahasiswa->nama} belum lengkap atau sudah terkunci.");
        }

        return back()->with('success', "Nilai {$mahasiswa->nama} berhasil dikunci (Final).");
    }

    /**
     * Kunci massal nilai mahasiswa bimbingan yang terpilih.
     */
    public function bulkLock(Request $request)
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer'],
        ], [
            'ids.required' => 'Pilih minimal satu mahasiswa terlebih dahulu.',
        ]);

        $dpl = Auth::user();
        $count = 0;

        AnggotaKelompok::with('penilaian')
            ->whereIn('id', $request->ids)
            ->whereHas('kelompok', fn ($q) => $q->where('dpl_id', $dpl->id))
            ->each(function ($mhs) use ($dpl, &$count) {
                $count += PenilaianService::kunci($mhs, $dpl) ? 1 : 0;
            });

        return back()->with('success', "{$count} mahasiswa berhasil dikunci. Nilai yang belum lengkap dilewati.");
    }

    public function export(Request $request)
    {
        $dpl = Auth::user();
        $query = PenilaianService::queryMahasiswa($dpl, $request->only(self::FILTERS));

        return RekapNilaiExportService::download($query, 'DPL: ' . $dpl->nama_lengkap);
    }

    private function pastikanBimbingan(?int $dplId): void
    {
        if ($dplId !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke mahasiswa/kelompok ini.');
        }
    }
}
