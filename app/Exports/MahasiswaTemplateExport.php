<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MahasiswaTemplateExport implements FromCollection, WithHeadings
{
    /**
     * Return sample collection data for Mahasiswa Import Template.
     */
    public function collection(): Collection
    {
        return collect([
            [
                'id_mahasiswa' => '',
                'nim' => '2022081001',
                'nama_mahasiswa' => 'Rahmat Hidayat',
                'jenis_kelamin' => 'Laki-laki',
                'program_studi' => 'Manajemen',
                'konsentrasi' => 'Pemasaran',
                'no_hp_whatsapp' => '081234567890',
                'alamat' => 'Jl. Siliwangi No. 12, Kuningan',
            ],
            [
                'id_mahasiswa' => '',
                'nim' => '2022081002',
                'nama_mahasiswa' => 'Siti Nurhaliza',
                'jenis_kelamin' => 'Perempuan',
                'program_studi' => 'Akuntansi',
                'konsentrasi' => 'Keuangan',
                'no_hp_whatsapp' => '085298765432',
                'alamat' => 'Jl. Veteran No. 45, Kuningan',
            ],
        ]);
    }

    /**
     * Return headings for Mahasiswa Import Template.
     */
    public function headings(): array
    {
        return [
            'ID Mahasiswa',
            'NIM',
            'Nama Mahasiswa',
            'Jenis Kelamin',
            'Program Studi',
            'Konsentrasi',
            'No HP / Whatsapp',
            'Alamat',
        ];
    }
}
