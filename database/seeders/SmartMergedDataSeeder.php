<?php

namespace Database\Seeders;

use App\Models\AnggotaKelompok;
use App\Models\ConfigAplikasi;
use App\Models\KelompokPpl;
use App\Models\Mitra;
use App\Models\PenilaianPpl;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class SmartMergedDataSeeder extends Seeder
{
    private const TABEL_MONITORING = ['users', 'mitra', 'kelompok_ppl', 'anggota_kelompok', 'config_aplikasi'];

    public function run(): void
    {
        $this->command?->info('=== MEMULAI SMART SEEDING DATA PPL FEB UNIKU ===');

        Schema::disableForeignKeyConstraints();

        // 1. Bersihkan tabel terkait terlebih dahulu
        DB::table('penilaian_ppl')->truncate();
        DB::table('anggota_kelompok')->truncate();
        DB::table('kelompok_ppl')->truncate();
        DB::table('mitra')->truncate();
        DB::table('users')->truncate();

        // 2. Impor Data Master dari Monitoring (data-master/file_backup_[22-08-2026].sql)
        $this->importMonitoringMasterData();

        // 3. Jalankan Sinkronisasi Data Ploting Final & MBKM
        $this->command?->info('Menjalankan penyelarasan ploting dan kelompok MBKM...');
        $sinkronisasi = new SinkronisasiDataPenilaianSeeder();
        $sinkronisasi->setCommand($this->command);
        $sinkronisasi->run();

        // 4. Impor Data Penilaian & Update Akun dari ppl_febuniku_backup_2026-09-28_035358.sql
        $this->importPenilaianAndUserUpdates();

        // 5. Pastikan Config Skala Nilai Default Tersedia
        $this->ensureConfigSkalaNilai();

        Schema::enableForeignKeyConstraints();

        $this->command?->info('=== SMART SEEDING SELESAI DENGAN SUKSES! ===');
    }

    private function importMonitoringMasterData(): void
    {
        $sqlPath = base_path('data-master/file_backup_[22-08-2026].sql');
        if (! file_exists($sqlPath)) {
            $this->command?->error("File backup monitoring tidak ditemukan: {$sqlPath}");
            return;
        }

        $this->command?->info('Membaca data master dari file_backup_[22-08-2026].sql...');

        $rows = array_fill_keys(self::TABEL_MONITORING, []);

        foreach (file($sqlPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if (! preg_match('/^INSERT INTO `(\w+)` \((.+?)\) VALUES \((.*)\);$/', $line, $m)) {
                continue;
            }

            $tabel = $m[1];
            if (! isset($rows[$tabel])) {
                continue;
            }

            $kolom = array_map(fn ($c) => trim($c, ' `'), explode(',', $m[2]));
            $values = PplRealDataSeeder::parseValues($m[3]);
            if (count($kolom) === count($values)) {
                $rows[$tabel][] = array_combine($kolom, $values);
            }
        }

        DB::transaction(function () use ($rows) {
            foreach (self::TABEL_MONITORING as $tabel) {
                if (empty($rows[$tabel])) {
                    continue;
                }

                $kolomTersedia = Schema::getColumnListing($tabel);
                foreach (array_chunk($rows[$tabel], 100) as $chunk) {
                    $chunk = array_map(fn ($row) => array_intersect_key($row, array_flip($kolomTersedia)), $chunk);
                    DB::table($tabel)->upsert($chunk, ['id']);
                }

                $this->command?->info(sprintf('  [Monitoring] %-18s : %d baris diimpor', $tabel, count($rows[$tabel])));
            }
        });
    }

    private function importPenilaianAndUserUpdates(): void
    {
        $sqlPath = base_path('ppl_febuniku_backup_2026-09-28_035358.sql');
        if (! file_exists($sqlPath)) {
            $this->command?->error("File backup penilaian tidak ditemukan: {$sqlPath}");
            return;
        }

        $this->command?->info('Membaca data penilaian & akun terbaru dari ppl_febuniku_backup_2026-09-28_035358.sql...');

        $fp = fopen($sqlPath, 'r');
        $currentTable = null;
        $columns = [];
        $userUpdates = 0;
        $penilaianCount = 0;

        // Ambil map NIM -> AnggotaKelompok untuk pencarian cepat
        $anggotaMap = AnggotaKelompok::with('kelompok')->get()->keyBy('nim');

        while (($line = fgets($fp)) !== false) {
            $line = trim($line);

            if (preg_match('/^INSERT INTO `([^`]+)` \((.+?)\) VALUES/i', $line, $m)) {
                $currentTable = $m[1];
                $columns = array_map(fn ($c) => trim($c, ' `'), explode(',', $m[2]));
                continue;
            }

            if (! $currentTable || ! str_starts_with($line, '(')) {
                continue;
            }

            // Bersihkan baris values
            $valStr = rtrim($line, ';,');
            if (str_starts_with($valStr, '(') && str_ends_with($valStr, ')')) {
                $valStr = substr($valStr, 1, -1);
            }

            $values = PplRealDataSeeder::parseValues($valStr);
            if (count($columns) !== count($values)) {
                continue;
            }

            $data = array_combine($columns, $values);

            // 1. Sinkronisasi Data Users (Akun DPL & Admin)
            if ($currentTable === 'users' || $currentTable === 'main.users') {
                $username = $data['username'] ?? null;
                if ($username) {
                    $user = User::withTrashed()->where('username', $username)->first();
                    if ($user) {
                        $updateData = [];
                        if (! empty($data['email'])) {
                            $updateData['email'] = $data['email'];
                        }
                        if (! empty($data['password'])) {
                            $updateData['password'] = $data['password'];
                        }
                        if (! empty($data['no_hp'])) {
                            $updateData['no_hp'] = $data['no_hp'];
                        }
                        if (! empty($data['nip_nidn'])) {
                            $updateData['nip_nidn'] = $data['nip_nidn'];
                        }
                        if (! empty($updateData)) {
                            $user->update($updateData);
                            $userUpdates++;
                        }
                    }
                }
            }

            // 2. Sinkronisasi Data Nilai Mahasiswa (tabel students)
            if ($currentTable === 'students' || $currentTable === 'main.students') {
                $nim = $data['nim'] ?? null;
                if (! $nim || ! isset($anggotaMap[$nim])) {
                    continue;
                }

                $anggota = $anggotaMap[$nim];
                $mitraScore = isset($data['mitra_score']) && is_numeric($data['mitra_score']) ? (float) $data['mitra_score'] : null;
                $dplScore = isset($data['dpl_score']) && is_numeric($data['dpl_score']) ? (float) $data['dpl_score'] : null;
                $finalScore = isset($data['final_score']) && is_numeric($data['final_score']) ? (float) $data['final_score'] : null;
                $letterGrade = $data['letter_grade'] ?? null;
                $statusRaw = strtolower($data['status'] ?? 'draft');
                $status = ($statusRaw === 'locked' || $statusRaw === 'final') ? 'locked' : 'draft';
                $updatedAt = $data['updated_at'] ?? now();

                // Hitung nilai akhir jika belum ada tapi nilai mitra & dpl terisi
                if ($finalScore === null && $mitraScore !== null && $dplScore !== null) {
                    $finalScore = round(($mitraScore * 0.60) + ($dplScore * 0.40), 2);
                }

                // Hitung huruf mutu jika belum ada
                if (! $letterGrade && $finalScore !== null) {
                    $letterGrade = PenilaianPpl::konversiNilaiHuruf($finalScore);
                }

                // DPL ID untuk locked_by
                $dplId = $anggota->kelompok?->dpl_id;

                DB::table('penilaian_ppl')->updateOrInsert(
                    ['anggota_kelompok_id' => $anggota->id],
                    [
                        'kelompok_id' => $anggota->kelompok_id,
                        'nilai_mitra' => $mitraScore,
                        'dinilai_mitra_at' => $mitraScore !== null ? $updatedAt : null,
                        'nilai_dpl' => $dplScore,
                        'dinilai_dpl_at' => $dplScore !== null ? $updatedAt : null,
                        'nilai_akhir' => $finalScore,
                        'nilai_huruf' => $letterGrade,
                        'status' => $status,
                        'locked_at' => $status === 'locked' ? $updatedAt : null,
                        'locked_by' => $status === 'locked' ? $dplId : null,
                        'created_at' => $data['created_at'] ?? now(),
                        'updated_at' => $updatedAt,
                    ]
                );

                $penilaianCount++;
            }
        }

        fclose($fp);

        $this->command?->info("  [Penilaian] Akun pengguna diperbarui: {$userUpdates} akun");
        $this->command?->info("  [Penilaian] Rekapitulasi nilai mahasiswa diimpor: {$penilaianCount} mahasiswa");
    }

    private function ensureConfigSkalaNilai(): void
    {
        ConfigAplikasi::set('skala_nilai_huruf', [
            ['min' => 81.00, 'max' => 100.00, 'huruf' => 'A'],
            ['min' => 75.00, 'max' => 80.99,  'huruf' => 'AB'],
            ['min' => 69.00, 'max' => 74.99,  'huruf' => 'B'],
            ['min' => 63.00, 'max' => 68.99,  'huruf' => 'BC'],
            ['min' => 57.00, 'max' => 62.99,  'huruf' => 'C'],
            ['min' => 51.00, 'max' => 56.99,  'huruf' => 'CD'],
            ['min' => 45.00, 'max' => 50.99,  'huruf' => 'D'],
            ['min' => 0.00,  'max' => 44.99,  'huruf' => 'E'],
        ]);
        $this->command?->info('  [Config] Skala nilai huruf mutu A-E terverifikasi.');
    }
}
