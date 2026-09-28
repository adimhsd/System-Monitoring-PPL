<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendaftaranDpl extends Model
{
    protected $table = 'pendaftaran_dpl';

    protected $fillable = [
        'nama_lengkap',
        'nip_nidn',
        'no_hp',
        'email',
        'file_surat_kesanggupan',
        'status',
        'catatan_admin',
        'user_id',
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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }
}
