<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianPpl extends Model
{
    use HasFactory;

    protected $table = 'penilaian_ppl';

    protected $fillable = [
        'anggota_kelompok_id',
        'kelompok_id',
        'mitra_skor_kedisiplinan',
        'mitra_skor_etika',
        'mitra_skor_kerjasama',
        'mitra_skor_hasil_kerja',
        'total_nilai_mitra',
        'catatan_mitra',
        'dpl_skor_kedisiplinan',
        'dpl_skor_etika',
        'dpl_skor_kerjasama',
        'dpl_skor_hasil_kerja',
        'total_nilai_dpl',
        'catatan_dpl',
        'nilai_huruf',
        'dinilai_at',
    ];

    protected function casts(): array
    {
        return [
            'mitra_skor_kedisiplinan' => 'decimal:2',
            'mitra_skor_etika' => 'decimal:2',
            'mitra_skor_kerjasama' => 'decimal:2',
            'mitra_skor_hasil_kerja' => 'decimal:2',
            'total_nilai_mitra' => 'decimal:2',
            'dpl_skor_kedisiplinan' => 'decimal:2',
            'dpl_skor_etika' => 'decimal:2',
            'dpl_skor_kerjasama' => 'decimal:2',
            'dpl_skor_hasil_kerja' => 'decimal:2',
            'total_nilai_dpl' => 'decimal:2',
            'nilai_akhir_angka' => 'decimal:2',
            'dinilai_at' => 'datetime',
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

    /**
     * Helper Konversi Nilai Angka ke Nilai Huruf berdasarkan Config Aplikasi.
     */
    public static function konversiNilaiHuruf(float $nilaiAngka): string
    {
        $skala = ConfigAplikasi::get('skala_nilai_huruf', [
            ['min' => 81.00, 'max' => 100.00, 'huruf' => 'A'],
            ['min' => 75.00, 'max' => 80.99,  'huruf' => 'AB'],
            ['min' => 69.00, 'max' => 74.99,  'huruf' => 'B'],
            ['min' => 63.00, 'max' => 68.99,  'huruf' => 'BC'],
            ['min' => 57.00, 'max' => 62.99,  'huruf' => 'C'],
            ['min' => 51.00, 'max' => 56.99,  'huruf' => 'CD'],
            ['min' => 45.00, 'max' => 50.99,  'huruf' => 'D'],
            ['min' => 0.00,  'max' => 44.99,  'huruf' => 'E'],
        ]);

        foreach ($skala as $item) {
            if ($nilaiAngka >= $item['min'] && $nilaiAngka <= $item['max']) {
                return $item['huruf'];
            }
        }

        return 'E';
    }
}
