<?php

namespace App\Exports;

use App\Models\AnggotaKelompok;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NilaiPplExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection(): Collection
    {
        return AnggotaKelompok::with(['kelompok.mitra', 'kelompok.dpl', 'penilaian'])->get();
    }

    public function headings(): array
    {
        return [
            'ID Mahasiswa',
            'NIM',
            'Nama Mahasiswa',
            'Program Studi',
            'Konsentrasi',
            'Nama Kelompok PPL',
            'Mitra Penempatan',
            'Dosen Pembimbing (DPL)',
            'Nilai Mitra (60%)',
            'Nilai DPL (40%)',
            'Nilai Akhir Angka',
            'Nilai Huruf',
            'Tanggal Dinilai',
        ];
    }

    public function map($mhs): array
    {
        $p = $mhs->penilaian;
        $nilaiMitra = $p ? $p->total_nilai_mitra : null;
        $nilaiDpl = $p ? $p->total_nilai_dpl : null;
        $nilaiAkhir = ($nilaiMitra !== null && $nilaiDpl !== null) ? round(($nilaiMitra * 0.60) + ($nilaiDpl * 0.40), 2) : null;

        return [
            $mhs->id,
            $mhs->nim,
            $mhs->nama,
            $mhs->prodi,
            $mhs->konsentrasi ?? '-',
            $mhs->kelompok->nama_kelompok ?? 'Belum Ada Kelompok',
            $mhs->kelompok->mitra->nama_mitra ?? '-',
            $mhs->kelompok->dpl->nama_lengkap ?? '-',
            $nilaiMitra ?? 'Belum Diisi',
            $nilaiDpl ?? 'Belum Diisi',
            $nilaiAkhir ?? '-',
            $p->nilai_huruf ?? '-',
            ($p && $p->dinilai_at) ? $p->dinilai_at->format('d/m/Y H:i') : '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
