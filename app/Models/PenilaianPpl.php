<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianPpl extends Model
{
    use HasFactory;

    public const BOBOT_MITRA = 0.60;

    public const BOBOT_DPL = 0.40;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_LOCKED = 'locked';

    public const SKALA_DEFAULT = [
        ['min' => 81.00, 'max' => 100.00, 'huruf' => 'A'],
        ['min' => 75.00, 'max' => 80.99,  'huruf' => 'AB'],
        ['min' => 69.00, 'max' => 74.99,  'huruf' => 'B'],
        ['min' => 63.00, 'max' => 68.99,  'huruf' => 'BC'],
        ['min' => 57.00, 'max' => 62.99,  'huruf' => 'C'],
        ['min' => 51.00, 'max' => 56.99,  'huruf' => 'CD'],
        ['min' => 45.00, 'max' => 50.99,  'huruf' => 'D'],
        ['min' => 0.00,  'max' => 44.99,  'huruf' => 'E'],
    ];

    protected $table = 'penilaian_ppl';

    protected $fillable = [
        'anggota_kelompok_id',
        'kelompok_id',
        'nilai_mitra',
        'catatan_mitra',
        'dinilai_mitra_at',
        'nilai_dpl',
        'catatan_dpl',
        'dinilai_dpl_at',
        'nilai_akhir',
        'nilai_huruf',
        'status',
        'locked_at',
        'locked_by',
    ];

    protected $attributes = [
        'status' => self::STATUS_DRAFT,
    ];

    protected function casts(): array
    {
        return [
            'nilai_mitra' => 'float',
            'nilai_dpl' => 'float',
            'nilai_akhir' => 'float',
            'dinilai_mitra_at' => 'datetime',
            'dinilai_dpl_at' => 'datetime',
            'locked_at' => 'datetime',
        ];
    }

    public function anggota()
    {
        return $this->belongsTo(AnggotaKelompok::class, 'anggota_kelompok_id');
    }

    public function kelompok()
    {
        return $this->belongsTo(KelompokPpl::class, 'kelompok_id');
    }

    public function lockedBy()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function isLocked(): bool
    {
        return $this->status === self::STATUS_LOCKED;
    }

    public function isLengkap(): bool
    {
        return $this->nilai_mitra !== null && $this->nilai_dpl !== null;
    }

    /**
     * Nilai Akhir = (Nilai Mitra x 60%) + (Nilai Laporan DPL x 40%).
     * Hanya dihitung jika kedua sumber nilai sudah terisi.
     */
    public static function hitungNilaiAkhir(?float $nilaiMitra, ?float $nilaiDpl): ?float
    {
        if ($nilaiMitra === null || $nilaiDpl === null) {
            return null;
        }

        return round(($nilaiMitra * self::BOBOT_MITRA) + ($nilaiDpl * self::BOBOT_DPL), 2);
    }

    public static function skalaNilaiHuruf(): array
    {
        return ConfigAplikasi::get('skala_nilai_huruf', self::SKALA_DEFAULT);
    }

    /**
     * Helper Konversi Nilai Angka ke Nilai Huruf berdasarkan Config Aplikasi.
     */
    public static function konversiNilaiHuruf(float $nilaiAngka, ?array $skala = null): string
    {
        foreach ($skala ?? self::skalaNilaiHuruf() as $item) {
            if ($nilaiAngka >= $item['min'] && $nilaiAngka <= $item['max']) {
                return $item['huruf'];
            }
        }

        return 'E';
    }

    protected static function booted(): void
    {
        static::saving(function (PenilaianPpl $penilaian) {
            $penilaian->nilai_akhir = self::hitungNilaiAkhir($penilaian->nilai_mitra, $penilaian->nilai_dpl);
            $penilaian->nilai_huruf = $penilaian->nilai_akhir !== null
                ? self::konversiNilaiHuruf($penilaian->nilai_akhir)
                : null;
        });
    }
}
