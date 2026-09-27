<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelompokPpl extends Model
{
    use HasFactory;

    protected $table = 'kelompok_ppl';

    protected $fillable = [
        'nama_kelompok',
        'mitra_id',
        'dpl_id',
        'ketua_user_id',
        'tahun_akademik',
        'status',
    ];

    public function mitra()
    {
        return $this->belongsTo(Mitra::class, 'mitra_id');
    }

    public function dpl()
    {
        return $this->belongsTo(User::class, 'dpl_id');
    }

    public function ketua()
    {
        return $this->belongsTo(User::class, 'ketua_user_id');
    }

    public function anggota()
    {
        return $this->hasMany(AnggotaKelompok::class, 'kelompok_id');
    }

    public function kegiatanHarian()
    {
        return $this->hasMany(KegiatanHarian::class, 'kelompok_id');
    }

    public function luaran()
    {
        return $this->hasOne(LuaranKelompok::class, 'kelompok_id');
    }

    public function penilaian()
    {
        return $this->hasOne(PenilaianPpl::class, 'kelompok_id');
    }

    public function monitoringDpl()
    {
        return $this->hasMany(MonitoringDpl::class, 'kelompok_id');
    }
}
