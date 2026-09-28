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

    public const KATEGORI_MBKM = 'MBKM';

    public function mitra()
    {
        return $this->belongsTo(Mitra::class, 'mitra_id');
    }

    /**
     * Kelompok Rekognisi PPL MBKM (mitra berkategori MBKM): tanpa logbook harian
     * dan Nilai Mitra diinput langsung oleh DPL.
     */
    public function isMbkm(): bool
    {
        if ($this->relationLoaded('mitra') && $this->mitra) {
            return $this->mitra->kategori === self::KATEGORI_MBKM;
        }

        return $this->mitra()->withTrashed()->where('kategori', self::KATEGORI_MBKM)->exists();
    }

    public function scopeMbkm($query)
    {
        return $query->whereHas('mitra', fn ($q) => $q->withTrashed()->where('kategori', self::KATEGORI_MBKM));
    }

    public function scopeNonMbkm($query)
    {
        return $query->whereDoesntHave('mitra', fn ($q) => $q->withTrashed()->where('kategori', self::KATEGORI_MBKM));
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
