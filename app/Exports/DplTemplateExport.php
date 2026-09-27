<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DplTemplateExport implements FromCollection, WithHeadings
{
    /**
     * Return sample collection data for DPL Import Template.
     */
    public function collection(): Collection
    {
        return collect([
            [
                'id_dpl' => '',
                'username' => 'DPL_PPL01',
                'password' => 'password123',
                'nip_nidn' => '198001012005011001',
                'nama_lengkap_dpl' => 'Dr. Ahmad Hidayat, M.Si',
                'no_hp_whatsapp' => '081298765432',
                'email' => 'ahmad.hidayat@uniku.ac.id',
                'status_akun' => 'Aktif',
            ],
            [
                'id_dpl' => '',
                'username' => 'DPL_PPL02',
                'password' => 'password123',
                'nip_nidn' => '198503152010122002',
                'nama_lengkap_dpl' => 'Sri Rahayu, S.E., M.Ak',
                'no_hp_whatsapp' => '081345678901',
                'email' => 'sri.rahayu@uniku.ac.id',
                'status_akun' => 'Aktif',
            ],
        ]);
    }

    /**
     * Return headings for DPL Import Template.
     */
    public function headings(): array
    {
        return [
            'ID DPL',
            'Username',
            'Password',
            'NIP / NIDN',
            'Nama Lengkap DPL',
            'No HP / Whatsapp',
            'Email',
            'Status Akun',
        ];
    }
}
