<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class KelompokTemplateExport implements FromCollection, WithHeadings
{
    /**
     * Return sample collection data for Kelompok Import Template.
     */
    public function collection(): Collection
    {
        return collect([
            [
                'id_kelompok' => '',
                'nama_kelompok' => 'Kelompok 01 - BAPPEDA',
                'tahun_akademik' => '2026/2027',
                'status_kelompok' => 'aktif',
                'username_ketua' => 'kelompok01',
                'password_ketua' => 'password123',
                'nama_dpl' => 'Dendi Purnama, SE., M.Si',
                'nama_mitra' => 'BAPPEDA Kabupaten Kuningan',
            ],
            [
                'id_kelompok' => '',
                'nama_kelompok' => 'Kelompok 02 - Tirta Utama',
                'tahun_akademik' => '2026/2027',
                'status_kelompok' => 'aktif',
                'username_ketua' => 'kelompok02',
                'password_ketua' => 'password123',
                'nama_dpl' => 'Dr. H. Amir, M.Si',
                'nama_mitra' => 'PT Tirta Utama Kuningan',
            ],
        ]);
    }

    /**
     * Return headings for Kelompok Import Template.
     */
    public function headings(): array
    {
        return [
            'ID Kelompok',
            'Nama Kelompok',
            'Tahun Akademik',
            'Status Kelompok',
            'Username Ketua (User)',
            'Password Ketua',
            'Nama DPL',
            'Nama Mitra',
        ];
    }
}
