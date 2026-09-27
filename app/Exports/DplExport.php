<?php

namespace App\Exports;

use App\Models\KelompokPpl;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DplExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection(): Collection
    {
        return User::where('role', 'dpl')->latest()->get();
    }

    public function headings(): array
    {
        return [
            'ID DPL',
            'Username',
            'NIP / NIDN',
            'Nama Lengkap DPL',
            'No HP / Whatsapp',
            'Email',
            'Status Akun',
            'Total Bimbingan Mahasiswa',
        ];
    }

    public function map($dpl): array
    {
        $totalMhs = KelompokPpl::where('dpl_id', $dpl->id)
            ->where('status', 'aktif')
            ->withCount('anggota')
            ->get()
            ->sum('anggota_count');

        return [
            $dpl->id,
            $dpl->username,
            $dpl->nip_nidn ?? '-',
            $dpl->nama_lengkap,
            $dpl->no_hp ?? '-',
            $dpl->email ?? '-',
            $dpl->is_active ? 'Aktif' : 'Non-Aktif',
            $totalMhs . ' Mahasiswa',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
