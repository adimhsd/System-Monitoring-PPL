<?php

namespace App\Exports;

use App\Models\Mitra;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MitraExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection(): Collection
    {
        return Mitra::with('picUser')->latest()->get();
    }

    public function headings(): array
    {
        return [
            'ID Mitra',
            'Nama Mitra Instansi',
            'Kategori',
            'Alamat',
            'Username PIC',
            'Nama PIC Mitra',
            'No HP PIC Mitra',
        ];
    }

    public function map($mitra): array
    {
        return [
            $mitra->id,
            $mitra->nama_mitra,
            $mitra->kategori,
            $mitra->alamat ?? '-',
            $mitra->picUser->username ?? '-',
            $mitra->picUser->nama_lengkap ?? 'Belum ada PIC',
            $mitra->picUser->no_hp ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
