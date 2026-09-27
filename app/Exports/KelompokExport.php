<?php

namespace App\Exports;

use App\Models\KelompokPpl;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KelompokExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection(): Collection
    {
        return KelompokPpl::with(['ketua', 'mitra', 'dpl', 'anggota'])->latest()->get();
    }

    public function headings(): array
    {
        return [
            'ID Kelompok',
            'Nama Kelompok',
            'Tahun Akademik',
            'Status Kelompok',
            'Username Ketua (User)',
            'Nama DPL Pembimbing',
            'Nama Mitra Instansi',
            'Jumlah Anggota Mahasiswa',
        ];
    }

    public function map($kelompok): array
    {
        return [
            $kelompok->id,
            $kelompok->nama_kelompok,
            $kelompok->tahun_akademik,
            ucfirst($kelompok->status ?? 'aktif'),
            $kelompok->ketua->username ?? '-',
            $kelompok->dpl->nama_lengkap ?? 'Belum Diplotkan',
            $kelompok->mitra->nama_mitra ?? 'Belum Diplotkan',
            $kelompok->anggota->count() . ' Mahasiswa',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
