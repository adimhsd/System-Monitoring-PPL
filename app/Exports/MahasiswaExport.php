<?php

namespace App\Exports;

use App\Models\Mahasiswa;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MahasiswaExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection(): Collection
    {
        return Mahasiswa::with('kelompok')->latest()->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'NIM',
            'Nama Mahasiswa',
            'Jenis Kelamin',
            'Program Studi',
            'Konsentrasi',
            'No HP / Whatsapp',
            'Alamat',
            'Kelompok PPL',
        ];
    }

    public function map($mhs): array
    {
        return [
            $mhs->id,
            $mhs->nim,
            $mhs->nama,
            $mhs->jenis_kelamin ?? 'Laki-laki',
            $mhs->prodi,
            $mhs->konsentrasi ?? '-',
            $mhs->no_hp ?? '-',
            $mhs->alamat ?? '-',
            $mhs->kelompok->nama_kelompok ?? 'Belum Diplotkan',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
