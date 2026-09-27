<?php

namespace App\Http\Controllers\Dpl;

use App\Http\Controllers\Controller;
use App\Models\KelompokPpl;
use App\Models\PenilaianPpl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenilaianController extends Controller
{
    /**
     * Tampilkan Kelompok Bimbingan DPL untuk Penilaian.
     */
    public function index()
    {
        $dplUser = Auth::user();

        $kelompokList = KelompokPpl::with(['anggota.penilaian', 'mitra.picUser', 'luaran'])
            ->where('dpl_id', $dplUser->id)
            ->where('status', 'aktif')
            ->get();

        return view('dpl.penilaian.index', compact('kelompokList'));
    }

    /**
     * Form Penilaian DPL Per Mahasiswa.
     */
    public function edit(KelompokPpl $kelompok)
    {
        $dplUser = Auth::user();
        if ($kelompok->dpl_id !== $dplUser->id) {
            abort(403, 'Anda tidak memiliki akses ke kelompok ini.');
        }

        $kelompok->load(['anggota.penilaian', 'mitra', 'luaran']);

        return view('dpl.penilaian.edit', compact('kelompok'));
    }

    /**
     * Simpan/Update Penilaian DPL Per Mahasiswa (Bobot 40%).
     */
    public function update(Request $request, KelompokPpl $kelompok)
    {
        $dplUser = Auth::user();
        if ($kelompok->dpl_id !== $dplUser->id) {
            abort(403, 'Anda tidak memiliki akses ke kelompok ini.');
        }

        $request->validate([
            'nilai' => ['required', 'array'],
            'nilai.*.kedisiplinan' => ['required', 'numeric', 'min:0', 'max:100'],
            'nilai.*.etika' => ['required', 'numeric', 'min:0', 'max:100'],
            'nilai.*.kerjasama' => ['required', 'numeric', 'min:0', 'max:100'],
            'nilai.*.hasil_kerja' => ['required', 'numeric', 'min:0', 'max:100'],
            'nilai.*.catatan' => ['nullable', 'string'],
        ], [
            'nilai.*.kedisiplinan.required' => 'Skor kedisiplinan & kehadiran wajib diisi (0-100).',
            'nilai.*.etika.required' => 'Skor etika & sikap kerja wajib diisi (0-100).',
            'nilai.*.kerjasama.required' => 'Skor kerjasama tim wajib diisi (0-100).',
            'nilai.*.hasil_kerja.required' => 'Skor kualitas hasil kerja wajib diisi (0-100).',
        ]);

        foreach ($request->nilai as $anggotaId => $data) {
            $kedisiplinan = (float) $data['kedisiplinan'];
            $etika = (float) $data['etika'];
            $kerjasama = (float) $data['kerjasama'];
            $hasilKerja = (float) $data['hasil_kerja'];

            $totalDpl = round(($kedisiplinan + $etika + $kerjasama + $hasilKerja) / 4, 2);

            $p = PenilaianPpl::firstOrNew([
                'anggota_kelompok_id' => $anggotaId,
                'kelompok_id' => $kelompok->id,
            ]);

            $p->dpl_skor_kedisiplinan = $kedisiplinan;
            $p->dpl_skor_etika = $etika;
            $p->dpl_skor_kerjasama = $kerjasama;
            $p->dpl_skor_hasil_kerja = $hasilKerja;
            $p->total_nilai_dpl = $totalDpl;
            $p->catatan_dpl = $data['catatan'] ?? null;
            $p->dinilai_at = now();

            // Calculate final score and grade letter
            if ($p->total_nilai_mitra !== null) {
                $nilaiAkhir = round(($p->total_nilai_mitra * 0.60) + ($totalDpl * 0.40), 2);
                $p->nilai_huruf = PenilaianPpl::konversiNilaiHuruf($nilaiAkhir);
            }

            $p->save();
        }

        return redirect()->route('dpl.penilaian.edit', $kelompok)
            ->with('success', 'Penilaian mahasiswa oleh DPL berhasil disimpan.');
    }
}
