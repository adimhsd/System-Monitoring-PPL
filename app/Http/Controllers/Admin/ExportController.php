<?php

namespace App\Http\Controllers\Admin;

use App\Exports\KelompokPplExport;
use App\Exports\MitraExport;
use App\Exports\NilaiPplExport;
use App\Http\Controllers\Controller;
use App\Models\AnggotaKelompok;
use App\Models\KelompokPpl;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    /**
     * Cetak Lembar Nilai Resmi Per Kelompok (PDF).
     */
    public function downloadLembarNilaiPdf(KelompokPpl $kelompok)
    {
        $kelompok->load(['mitra.picUser', 'dpl', 'ketua', 'anggota.penilaian']);

        $pdf = Pdf::loadView('pdf.lembar-nilai', compact('kelompok'))
            ->setPaper('a4', 'portrait');

        $fileName = 'Lembar_Nilai_' . \Illuminate\Support\Str::slug($kelompok->nama_kelompok) . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Cetak Rekapitulasi Nilai PPL Mahasiswa Fakultas (PDF Landscape).
     */
    public function downloadRekapNilaiPdf()
    {
        $mahasiswaList = AnggotaKelompok::with(['kelompok.mitra', 'kelompok.dpl', 'penilaian'])->get();

        $pdf = Pdf::loadView('pdf.rekap-nilai', compact('mahasiswaList'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('Rekapitulasi_Nilai_PPL_FEB_UNIKU.pdf');
    }

    /**
     * Export Excel Rekap Nilai PPL.
     */
    public function exportNilaiExcel()
    {
        return Excel::download(new NilaiPplExport, 'Rekapitulasi_Nilai_PPL_FEB.xlsx');
    }

    /**
     * Export Excel Kelompok & Plotting PPL.
     */
    public function exportKelompokExcel()
    {
        return Excel::download(new KelompokPplExport, 'Data_Kelompok_Plotting_PPL.xlsx');
    }

    /**
     * Export Excel Master Data Mitra.
     */
    public function exportMitraExcel()
    {
        return Excel::download(new MitraExport, 'Master_Data_Mitra_PPL.xlsx');
    }
}
