<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'anggota_kelompok';

    protected $fillable = [
        'kelompok_id',
        'nim',
        'nama',
        'jenis_kelamin',
        'prodi',
        'konsentrasi',
        'no_hp',
        'alamat',
    ];

    public function kelompok()
    {
        return $this->belongsTo(KelompokPpl::class, 'kelompok_id');
    }

    public function penilaian()
    {
        return $this->hasOne(PenilaianPpl::class, 'anggota_kelompok_id');
    }
}
