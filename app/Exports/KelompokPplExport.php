<?php

namespace App\Exports;

use App\Models\KelompokPpl;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KelompokPplExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection(): Collection
    {
        return KelompokPpl::with(['mitra', 'dpl', 'ketua', 'anggota'])->get();
    }

    public function headings(): array
    {
        return [
            'ID Kelompok',
            'Nama Kelompok',
            'Tahun Akademik',
            'Status',
            'Mitra Penempatan',
            'Kategori Mitra',
            'Dosen Pembimbing (DPL)',
            'Ketua Kelompok',
            'Jumlah Anggota Mahasiswa',
        ];
    }

    public function map($kelompok): array
    {
        return [
            $kelompok->id,
            $kelompok->nama_kelompok,
            $kelompok->tahun_akademik,
            ucfirst($kelompok->status),
            $kelompok->mitra->nama_mitra ?? '-',
            $kelompok->mitra->kategori ?? '-',
            $kelompok->dpl->nama_lengkap ?? '-',
            $kelompok->ketua->nama_lengkap ?? '-',
            $kelompok->anggota->count(),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
