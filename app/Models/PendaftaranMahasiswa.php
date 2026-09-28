<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendaftaranMahasiswa extends Model
{
    public const PROGRAM_MBKM = [
        'Magang / Praktik Kerja',
        'Studi Independen',
        'Kampus Mengajar',
        'Wirausaha Merdeka',
        'Pertukaran Mahasiswa',
        'Proyek Kemanusiaan',
        'Penelitian / Riset',
        'Lainnya',
    ];

    public const KONSENTRASI = ['Pemasaran', 'Operasional', 'Keuangan', 'SDM', 'Akuntansi', 'Bisnis Digital'];

    protected $table = 'pendaftaran_mahasiswa';

    protected $fillable = [
        'jenis_ppl',
        'nim',
        'nama',
        'jenis_kelamin',
        'prodi',
        'konsentrasi',
        'no_hp',
        'email',
        'alamat',
        'program_mbkm',
        'instansi_mbkm',
        'file_bukti_pembayaran',
        'status',
        'catatan_admin',
        'anggota_kelompok_id',
        'diverifikasi_oleh',
        'diverifikasi_at',
    ];

    protected $attributes = [
        'status' => 'menunggu',
    ];

    protected function casts(): array
    {
        return ['diverifikasi_at' => 'datetime'];
    }

    public function anggota()
    {
        return $this->belongsTo(AnggotaKelompok::class, 'anggota_kelompok_id');
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    public function isMbkm(): bool
    {
        return $this->jenis_ppl === 'mbkm';
    }
}
