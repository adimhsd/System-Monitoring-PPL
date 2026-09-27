<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mitra extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mitra';

    protected $fillable = [
        'nama_mitra',
        'kategori',
        'alamat',
        'pic_user_id',
    ];

    public function picUser()
    {
        return $this->belongsTo(User::class, 'pic_user_id');
    }

    public function kelompokPpl()
    {
        return $this->hasMany(KelompokPpl::class, 'mitra_id');
    }
}
