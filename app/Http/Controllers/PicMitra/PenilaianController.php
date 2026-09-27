<?php

namespace App\Http\Controllers\PicMitra;

use App\Http\Controllers\Controller;
use App\Models\KelompokPpl;
use App\Models\Mitra;
use App\Models\PenilaianPpl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenilaianController extends Controller
{
    /**
     * Tampilkan Daftar Kelompok & Mahasiswa untuk Penilaian PIC Mitra.
     */
    public function index()
    {
        $picUser = Auth::user();
        $mitra = Mitra::where('pic_user_id', $picUser->id)->first();

        if (! $mitra) {
            return view('pic.penilaian.index', ['kelompokList' => collect()]);
        }

        $kelompokList = KelompokPpl::with(['anggota.penilaian', 'dpl', 'ketua'])
            ->where('mitra_id', $mitra->id)
            ->where('status', 'aktif')
            ->get();

        return view('pic.penilaian.index', compact('kelompokList'));
    }

    /**
     * Simpan/Update Penilaian PIC Mitra Per Mahasiswa (Bobot 60%).
     */
    public function storeOrUpdate(Request $request, KelompokPpl $kelompok)
    {
        $picUser = Auth::user();
        if ($kelompok->mitra->pic_user_id !== $picUser->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk menilai kelompok ini.');
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

            $totalMitra = round(($kedisiplinan + $etika + $kerjasama + $hasilKerja) / 4, 2);

            $p = PenilaianPpl::firstOrNew([
                'anggota_kelompok_id' => $anggotaId,
                'kelompok_id' => $kelompok->id,
            ]);

            $p->mitra_skor_kedisiplinan = $kedisiplinan;
            $p->mitra_skor_etika = $etika;
            $p->mitra_skor_kerjasama = $kerjasama;
            $p->mitra_skor_hasil_kerja = $hasilKerja;
            $p->total_nilai_mitra = $totalMitra;
            $p->catatan_mitra = $data['catatan'] ?? null;
            $p->dinilai_at = now();

            // Re-calculate grade if DPL score is present
            if ($p->total_nilai_dpl !== null) {
                $nilaiAkhir = round(($totalMitra * 0.60) + ($p->total_nilai_dpl * 0.40), 2);
                $p->nilai_huruf = PenilaianPpl::konversiNilaiHuruf($nilaiAkhir);
            }

            $p->save();
        }

        return redirect()->route('pic.penilaian.index')
            ->with('success', 'Penilaian mahasiswa oleh PIC Mitra (60%) berhasil disimpan.');
    }
}
