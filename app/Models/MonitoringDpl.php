<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringDpl extends Model
{
    use HasFactory;

    protected $table = 'monitoring_dpl';

    protected $fillable = [
        'dpl_user_id',
        'kelompok_id',
        'jenis_kunjungan',
        'tanggal_kunjungan',
        'waktu_kunjungan',
        'catatan_kunjungan',
        'foto_kunjungan',
        'disetujui_kelompok',
        'tanggal_disetujui',
    ];

    protected $casts = [
        'tanggal_kunjungan' => 'date',
        'tanggal_disetujui' => 'datetime',
        'disetujui_kelompok' => 'boolean',
    ];

    public function dpl()
    {
        return $this->belongsTo(User::class, 'dpl_user_id');
    }

    public function kelompok()
    {
        return $this->belongsTo(KelompokPpl::class, 'kelompok_id');
    }
}
