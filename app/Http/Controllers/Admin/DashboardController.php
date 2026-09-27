<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnggotaKelompok;
use App\Models\KelompokPpl;
use App\Models\LuaranKelompok;
use App\Models\Mahasiswa;
use App\Models\Mitra;
use App\Models\User;
use App\Services\PenilaianService;
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

        // 1. Rekapitulasi Ringkasan Penilaian PPL Mahasiswa (Input & Rekap Nilai)
        $stat = PenilaianService::statistik(PenilaianService::queryMahasiswa(auth()->user()));

        $dplSudahCount = KelompokPpl::whereNotNull('dpl_id')
            ->whereHas('anggota.penilaian', fn ($q) => $q->whereNotNull('nilai_dpl'))
            ->distinct()
            ->count('dpl_id');

        $mitraSudahCount = KelompokPpl::whereNotNull('mitra_id')
            ->whereHas('anggota.penilaian', fn ($q) => $q->whereNotNull('nilai_mitra'))
            ->distinct()
            ->count('mitra_id');

        $rekapPenilaian = [
            'mhs_sudah' => $stat['mhs_lengkap'],
            'mhs_belum' => $stat['mhs_belum'],
            'total_mhs' => $stat['total_mahasiswa'],
            'terkunci' => $stat['terkunci'],
            'dpl_sudah' => $dplSudahCount,
            'total_dpl' => $totalDpl,
            'mitra_sudah' => $mitraSudahCount,
            'total_mitra' => $totalMitra,
            'rata_rata' => $stat['rata_rata'],
            'huruf' => $stat['rekap_huruf'],
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
