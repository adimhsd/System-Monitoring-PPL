<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LuaranKelompok extends Model
{
    use HasFactory;

    protected $table = 'luaran_kelompok';

    protected $fillable = [
        'kelompok_id',
        'file_laporan_pdf',
        'url_video',
        'uploaded_at',
    ];

    protected function casts(): array
    {
        return [
            'uploaded_at' => 'datetime',
        ];
    }

    public function kelompok()
    {
        return $this->belongsTo(KelompokPpl::class, 'kelompok_id');
    }
}
