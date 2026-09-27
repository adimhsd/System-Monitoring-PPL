<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnggotaKelompok;
use App\Models\ConfigAplikasi;
use App\Models\KelompokPpl;
use App\Models\Mitra;
use App\Models\PenilaianPpl;
use App\Models\User;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    /**
     * Tampilkan Rekapitulasi Nilai PPL Per Mahasiswa Fakultas & Ringkasan Statistik.
     */
    public function index(Request $request)
    {
        $query = AnggotaKelompok::with(['kelompok.mitra', 'kelompok.dpl', 'penilaian']);

        if ($request->filled('kelompok_id')) {
            $query->where('kelompok_id', $request->kelompok_id);
        }

        if ($request->filled('prodi')) {
            $query->where('prodi', $request->prodi);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $mahasiswaList = $query->latest()->paginate(20)->withQueryString();
        $kelompokList = KelompokPpl::all();

        // 1. Ringkasan Statistik Mahasiswa Dinilai
        $totalMahasiswa = AnggotaKelompok::count();
        $allPenilaian = PenilaianPpl::all();

        $mhsSudahLengkap = $allPenilaian->filter(function ($p) {
            return $p->total_nilai_mitra !== null && $p->total_nilai_dpl !== null;
        })->count();

        $mhsParsial = $allPenilaian->filter(function ($p) {
            return ($p->total_nilai_mitra !== null && $p->total_nilai_dpl === null)
                || ($p->total_nilai_mitra === null && $p->total_nilai_dpl !== null);
        })->count();

        $mhsBelumDinilai = max(0, $totalMahasiswa - $mhsSudahLengkap);

        // 2. Ringkasan Status Penilaian DPL
        $totalDplCount = User::where('role', 'dpl')->count();
        $dplSudahCount = KelompokPpl::whereNotNull('dpl_id')
            ->whereHas('anggota.penilaian', function ($q) {
                $q->whereNotNull('total_nilai_dpl');
            })
            ->pluck('dpl_id')
            ->unique()
            ->count();
        $dplBelumCount = max(0, $totalDplCount - $dplSudahCount);

        // 3. Ringkasan Status Penilaian PIC Mitra
        $totalMitraCount = Mitra::count();
        $mitraSudahCount = KelompokPpl::whereNotNull('mitra_id')
            ->whereHas('anggota.penilaian', function ($q) {
                $q->whereNotNull('total_nilai_mitra');
            })
            ->pluck('mitra_id')
            ->unique()
            ->count();
        $mitraBelumCount = max(0, $totalMitraCount - $mitraSudahCount);

        // 4. Rekap Rata-rata Nilai & Frekuensi Huruf Mutu
        $nilaiAkhirList = [];
        $rekapHurufCounts = [
            'A'  => 0,
            'AB' => 0,
            'B'  => 0,
            'BC' => 0,
            'C'  => 0,
            'CD' => 0,
            'D'  => 0,
            'E'  => 0,
        ];

        foreach ($allPenilaian as $p) {
            if ($p->total_nilai_mitra !== null && $p->total_nilai_dpl !== null) {
                $nilaiAkhir = round(($p->total_nilai_mitra * 0.60) + ($p->total_nilai_dpl * 0.40), 2);
                $nilaiAkhirList[] = $nilaiAkhir;
                $huruf = $p->nilai_huruf ?? PenilaianPpl::konversiNilaiHuruf($nilaiAkhir);
                if (isset($rekapHurufCounts[$huruf])) {
                    $rekapHurufCounts[$huruf]++;
                } else {
                    $rekapHurufCounts[$huruf] = 1;
                }
            }
        }

        $rataRataNilai = count($nilaiAkhirList) > 0
            ? round(array_sum($nilaiAkhirList) / count($nilaiAkhirList), 2)
            : 0;

        $statsSummary = [
            'total_mahasiswa' => $totalMahasiswa,
            'mhs_sudah_lengkap' => $mhsSudahLengkap,
            'mhs_parsial' => $mhsParsial,
            'mhs_belum' => $mhsBelumDinilai,
            'total_dpl' => $totalDplCount,
            'dpl_sudah' => $dplSudahCount,
            'dpl_belum' => $dplBelumCount,
            'total_mitra' => $totalMitraCount,
            'mitra_sudah' => $mitraSudahCount,
            'mitra_belum' => $mitraBelumCount,
            'rata_rata_nilai' => $rataRataNilai,
            'rekap_huruf' => $rekapHurufCounts,
        ];

        $skalaHuruf = ConfigAplikasi::get('skala_nilai_huruf', [
            ['min' => 81.00, 'max' => 100.00, 'huruf' => 'A'],
            ['min' => 75.00, 'max' => 80.99,  'huruf' => 'AB'],
            ['min' => 69.00, 'max' => 74.99,  'huruf' => 'B'],
            ['min' => 63.00, 'max' => 68.99,  'huruf' => 'BC'],
            ['min' => 57.00, 'max' => 62.99,  'huruf' => 'C'],
            ['min' => 51.00, 'max' => 56.99,  'huruf' => 'CD'],
            ['min' => 45.00, 'max' => 50.99,  'huruf' => 'D'],
            ['min' => 0.00,  'max' => 44.99,  'huruf' => 'E'],
        ]);

        return view('admin.penilaian.index', compact('mahasiswaList', 'kelompokList', 'skalaHuruf', 'statsSummary'));
    }

    /**
     * Update Konfigurasi Skala Nilai Huruf.
     */
    public function updateGradeScale(Request $request)
    {
        $request->validate([
            'skala' => ['required', 'array'],
            'skala.*.huruf' => ['required', 'string'],
            'skala.*.min' => ['required', 'numeric', 'min:0', 'max:100'],
            'skala.*.max' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        ConfigAplikasi::set('skala_nilai_huruf', $request->skala);

        // Recalculate all letter grades
        $penilaianList = PenilaianPpl::all();
        foreach ($penilaianList as $p) {
            if ($p->total_nilai_mitra !== null && $p->total_nilai_dpl !== null) {
                $nilaiAkhir = round(($p->total_nilai_mitra * 0.60) + ($p->total_nilai_dpl * 0.40), 2);
                $p->update(['nilai_huruf' => PenilaianPpl::konversiNilaiHuruf($nilaiAkhir)]);
            }
        }

        return redirect()->route('admin.penilaian.index')
            ->with('success', 'Konfigurasi skala nilai huruf berhasil diperbarui & diterapkan ulang.');
    }
}
