<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Seeder data riil PPL FEB UNIKU untuk instalasi baru:
 *  1. Memuat akun, mitra, kelompok, dan mahasiswa dari backup Monitoring
 *     (data-master/file_backup_[22-08-2026].sql).
 *  2. Menerapkan pembaruan data SystemPenilaianPPL (ploting final & MBKM).
 *
 * Jalankan setelah migrate:fresh:
 *   php artisan db:seed --class=PplRealDataSeeder
 */
class PplRealDataSeeder extends Seeder
{
    private const TABEL = ['users', 'mitra', 'kelompok_ppl', 'anggota_kelompok', 'config_aplikasi'];

    public function run(): void
    {
        $sqlPath = base_path('data-master/file_backup_[22-08-2026].sql');

        if (! file_exists($sqlPath)) {
            $this->command?->error("File backup SQL tidak ditemukan di: {$sqlPath}");

            return;
        }

        $rows = array_fill_keys(self::TABEL, []);

        foreach (file($sqlPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            if (! preg_match('/^INSERT INTO `(\w+)` \((.+?)\) VALUES \((.*)\);$/', trim($line), $m) || ! isset($rows[$m[1]])) {
                continue;
            }

            $kolom = array_map(fn ($c) => trim($c, ' `'), explode(',', $m[2]));
            $rows[$m[1]][] = array_combine($kolom, self::parseValues($m[3]));
        }

        Schema::disableForeignKeyConstraints();

        DB::transaction(function () use ($rows) {
            foreach (self::TABEL as $tabel) {
                $kolomTersedia = Schema::getColumnListing($tabel);

                foreach (array_chunk($rows[$tabel], 100) as $chunk) {
                    $chunk = array_map(fn ($row) => array_intersect_key($row, array_flip($kolomTersedia)), $chunk);
                    DB::table($tabel)->upsert($chunk, ['id']);
                }

                $this->command?->info(sprintf('%-18s : %d baris', $tabel, count($rows[$tabel])));
            }
        });

        Schema::enableForeignKeyConstraints();

        $this->call(SinkronisasiDataPenilaianSeeder::class);
    }

    /**
     * Parse daftar nilai SQL: 'teks', NULL, 123 (escape gaya MySQL).
     */
    public static function parseValues(string $input): array
    {
        $values = [];
        $len = strlen($input);
        $i = 0;

        while ($i < $len) {
            $char = $input[$i];

            if ($char === ' ' || $char === ',') {
                $i++;
                continue;
            }

            if ($char === "'") {
                $buffer = '';
                $i++;
                while ($i < $len) {
                    $c = $input[$i];
                    if ($c === '\\' && $i + 1 < $len) {
                        $next = $input[$i + 1];
                        $buffer .= match ($next) {
                            'n' => "\n",
                            'r' => "\r",
                            't' => "\t",
                            '0' => "\0",
                            default => $next,
                        };
                        $i += 2;
                        continue;
                    }
                    if ($c === "'") {
                        if ($i + 1 < $len && $input[$i + 1] === "'") {
                            $buffer .= "'";
                            $i += 2;
                            continue;
                        }
                        $i++;
                        break;
                    }
                    $buffer .= $c;
                    $i++;
                }
                $values[] = $buffer;
                continue;
            }

            $end = strpos($input, ',', $i);
            $token = trim($end === false ? substr($input, $i) : substr($input, $i, $end - $i));
            $values[] = strtoupper($token) === 'NULL' ? null : $token;
            $i = $end === false ? $len : $end;
        }

        return $values;
    }
}
