<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KegiatanHarian extends Model
{
    use HasFactory;

    protected $table = 'kegiatan_harian';

    protected $fillable = [
        'kelompok_id',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'deskripsi_kegiatan',
        'foto_dokumentasi',
        'dilihat_mitra',
        'dilihat_mitra_at',
        'status_validasi_mitra',
        'catatan_mitra',
        'dilihat_dpl',
        'dilihat_dpl_at',
        'terlambat',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'dilihat_mitra' => 'boolean',
            'dilihat_mitra_at' => 'datetime',
            'dilihat_dpl' => 'boolean',
            'dilihat_dpl_at' => 'datetime',
            'terlambat' => 'boolean',
        ];
    }

    public function kelompok()
    {
        return $this->belongsTo(KelompokPpl::class, 'kelompok_id');
    }
}
