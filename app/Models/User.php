<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'username',
        'password',
        'role',
        'nama_lengkap',
        'no_hp',
        'nip_nidn',
        'email',
        'must_change_password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'must_change_password' => 'boolean',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function mitraPic()
    {
        return $this->hasOne(Mitra::class, 'pic_user_id');
    }

    public function kelompokDpl()
    {
        return $this->hasMany(KelompokPpl::class, 'dpl_id');
    }

    public function kelompokKetua()
    {
        return $this->hasOne(KelompokPpl::class, 'ketua_user_id');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'user_id');
    }

    public function monitoringDpl()
    {
        return $this->hasMany(MonitoringDpl::class, 'dpl_user_id');
    }
}
