<?php

namespace App\Exports;

use App\Models\KelompokPpl;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PlottingPplExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected ?string $search;

    public function __construct(?string $search = null)
    {
        $this->search = $search;
    }

    public function collection(): Collection
    {
        $query = KelompokPpl::with(['mitra.picUser', 'dpl', 'ketua', 'anggota']);

        if (! empty($this->search)) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_kelompok', 'like', "%{$search}%")
                  ->orWhereHas('mitra', function ($mq) use ($search) {
                      $mq->where('nama_mitra', 'like', "%{$search}%");
                  })
                  ->orWhereHas('dpl', function ($dq) use ($search) {
                      $dq->where('nama_lengkap', 'like', "%{$search}%");
                  });
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID Kelompok',
            'Nama Kelompok PPL',
            'Username Akun',
            'Tahun Akademik',
            'Status Kelompok',
            'Mitra Penempatan',
            'Kategori Mitra',
            'Alamat Mitra',
            'Pembimbing PIC Mitra',
            'Dosen Pembimbing (DPL)',
            'NIP / NIDN DPL',
            'Jumlah Anggota Mahasiswa',
            'Daftar Anggota Mahasiswa (NIM - Nama - Prodi)',
        ];
    }

    public function map($kelompok): array
    {
        $daftarAnggota = $kelompok->anggota->map(function ($mhs) {
            return "{$mhs->nim} - {$mhs->nama} ({$mhs->prodi})";
        })->join('; ');

        return [
            $kelompok->id,
            $kelompok->nama_kelompok,
            $kelompok->ketua->username ?? '-',
            $kelompok->tahun_akademik,
            ucfirst($kelompok->status),
            $kelompok->mitra->nama_mitra ?? 'Belum Diplotkan',
            $kelompok->mitra->kategori ?? '-',
            $kelompok->mitra->alamat ?? '-',
            $kelompok->mitra->picUser->nama_lengkap ?? '-',
            $kelompok->dpl->nama_lengkap ?? 'Belum Diplotkan',
            $kelompok->dpl->nip_nidn ?? '-',
            $kelompok->anggota->count(),
            $daftarAnggota ?: 'Belum Ada Anggota',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
