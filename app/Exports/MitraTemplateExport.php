<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MitraTemplateExport implements FromCollection, WithHeadings
{
    /**
     * Return sample collection data for Mitra Import Template.
     */
    public function collection(): Collection
    {
        return collect([
            [
                'id_mitra' => '',
                'nama_mitra_instansi' => 'BAPPEDA Kabupaten Kuningan',
                'kategori' => 'SKPD',
                'alamat' => 'Jl. Soekarno No. 1, Kuningan',
                'nama_pic_mitra' => 'Haji Sobri, S.T.',
                'username_pic' => 'pic_bappeda',
                'password_pic' => 'password123',
                'no_hp_pic_mitra' => '085211223344',
            ],
            [
                'id_mitra' => '',
                'nama_mitra_instansi' => 'PT Tirta Utama Kuningan',
                'kategori' => 'Swasta',
                'alamat' => 'Jl. Raya Cilimus No. 45, Kuningan',
                'nama_pic_mitra' => 'Anita Wijaya, S.E.',
                'username_pic' => 'pic_tirta',
                'password_pic' => 'password123',
                'no_hp_pic_mitra' => '085322334455',
            ],
        ]);
    }

    /**
     * Return headings for Mitra Import Template.
     */
    public function headings(): array
    {
        return [
            'ID Mitra',
            'Nama Mitra Instansi',
            'Kategori',
            'Alamat',
            'Nama PIC Mitra',
            'Username PIC',
            'Password PIC',
            'No HP PIC',
        ];
    }
}
