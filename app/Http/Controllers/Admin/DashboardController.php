<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnggotaKelompok;
use App\Models\KelompokPpl;
use App\Models\LuaranKelompok;
use App\Models\Mahasiswa;
use App\Models\Mitra;
use App\Models\PenilaianPpl;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalMahasiswa = Mahasiswa::count();
        $totalMahasiswaPlotting = Mahasiswa::whereNotNull('kelompok_id')->count();
        $totalMahasiswaUnassigned = Mahasiswa::whereNull('kelompok_id')->count();

        $totalKelompok = KelompokPpl::count();
        $totalMitra = Mitra::count();
        $totalDpl = User::where('role', 'dpl')->count();

        // Widget Logbook Warning: Kelompok aktif yang belum mengisi logbook > 24 jam
        $today = Carbon::today();
        $kelompokBelumIsiLogbook = KelompokPpl::with(['mitra', 'ketua', 'dpl'])
            ->where('status', 'aktif')
            ->whereDoesntHave('kegiatanHarian', function ($q) use ($today) {
                $q->where('tanggal', $today->format('Y-m-d'));
            })
            ->get();

        // 1. Rekapitulasi Ringkasan Penilaian PPL Mahasiswa
        $allPenilaian = PenilaianPpl::all();
        $totalAnggotaMhs = AnggotaKelompok::count();

        $mhsSudahLengkap = $allPenilaian->filter(function ($p) {
            return $p->total_nilai_mitra !== null && $p->total_nilai_dpl !== null;
        })->count();
        $mhsBelumDinilai = max(0, $totalAnggotaMhs - $mhsSudahLengkap);

        $dplSudahCount = KelompokPpl::whereNotNull('dpl_id')
            ->whereHas('anggota.penilaian', function ($q) {
                $q->whereNotNull('total_nilai_dpl');
            })
            ->pluck('dpl_id')
            ->unique()
            ->count();

        $mitraSudahCount = KelompokPpl::whereNotNull('mitra_id')
            ->whereHas('anggota.penilaian', function ($q) {
                $q->whereNotNull('total_nilai_mitra');
            })
            ->pluck('mitra_id')
            ->unique()
            ->count();

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

        $rekapPenilaian = [
            'mhs_sudah' => $mhsSudahLengkap,
            'mhs_belum' => $mhsBelumDinilai,
            'total_mhs' => $totalAnggotaMhs,
            'dpl_sudah' => $dplSudahCount,
            'total_dpl' => $totalDpl,
            'mitra_sudah' => $mitraSudahCount,
            'total_mitra' => $totalMitra,
            'rata_rata' => $rataRataNilai,
            'huruf' => $rekapHurufCounts,
        ];

        // 2. Rekapitulasi Ringkasan Luaran Akhir PPL Fakultas
        $luaranList = LuaranKelompok::all();

        $luaranLengkapCount = $luaranList->filter(function ($l) {
            return !empty($l->file_laporan_pdf) && !empty($l->url_video);
        })->count();

        $luaranParsialCount = $luaranList->filter(function ($l) {
            return (!empty($l->file_laporan_pdf) && empty($l->url_video))
                || (empty($l->file_laporan_pdf) && !empty($l->url_video));
        })->count();

        $luaranBelumCount = max(0, $totalKelompok - ($luaranLengkapCount + $luaranParsialCount));

        $pdfTerkumpulCount = $luaranList->filter(function ($l) {
            return !empty($l->file_laporan_pdf);
        })->count();

        $videoTerkumpulCount = $luaranList->filter(function ($l) {
            return !empty($l->url_video);
        })->count();

        $persentaseLuaran = $totalKelompok > 0
            ? round(($luaranLengkapCount / $totalKelompok) * 100, 1)
            : 0;

        $rekapLuaran = [
            'luaran_lengkap' => $luaranLengkapCount,
            'luaran_parsial' => $luaranParsialCount,
            'luaran_belum' => $luaranBelumCount,
            'total_kelompok' => $totalKelompok,
            'pdf_terkumpul' => $pdfTerkumpulCount,
            'video_terkumpul' => $videoTerkumpulCount,
            'persentase' => $persentaseLuaran,
        ];

        return view('admin.dashboard', compact(
            'totalMahasiswa',
            'totalMahasiswaPlotting',
            'totalMahasiswaUnassigned',
            'totalKelompok',
            'totalMitra',
            'totalDpl',
            'kelompokBelumIsiLogbook',
            'rekapPenilaian',
            'rekapLuaran'
        ));
    }
}
