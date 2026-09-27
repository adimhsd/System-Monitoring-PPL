<?php

namespace App\Services;

use App\Models\AnggotaKelompok;
use App\Models\KelompokPpl;
use App\Models\PenilaianPpl;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

/**
 * Logika penilaian PPL (diadopsi dari SystemPenilaianPPL):
 * Nilai Akhir = (Nilai Mitra x 60%) + (Nilai Laporan DPL x 40%),
 * status Draft / Terkunci, dan hanya Admin yang dapat membuka kunci.
 */
class PenilaianService
{
    public const HURUF_MUTU = ['A', 'AB', 'B', 'BC', 'C', 'CD', 'D', 'E'];

    /**
     * Query mahasiswa beserta penilaiannya. Untuk DPL otomatis dibatasi
     * pada kelompok bimbingannya sendiri.
     */
    public static function queryMahasiswa(User $user, array $filters = []): Builder
    {
        $query = AnggotaKelompok::query()
            ->with(['kelompok.mitra', 'kelompok.dpl', 'penilaian'])
            ->whereNotNull('kelompok_id');

        if ($user->role === 'dpl') {
            $query->whereHas('kelompok', fn ($q) => $q->where('dpl_id', $user->id));
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['prodi'])) {
            $query->where('prodi', $filters['prodi']);
        }

        if (! empty($filters['kelompok_id'])) {
            $query->where('kelompok_id', $filters['kelompok_id']);
        }

        if (! empty($filters['dpl_id'])) {
            $query->whereHas('kelompok', fn ($q) => $q->where('dpl_id', $filters['dpl_id']));
        }

        switch ($filters['status'] ?? null) {
            case 'locked':
                $query->whereHas('penilaian', fn ($q) => $q->where('status', PenilaianPpl::STATUS_LOCKED));
                break;
            case 'draft':
                $query->whereHas('penilaian', fn ($q) => $q->where('status', PenilaianPpl::STATUS_DRAFT)->whereNotNull('nilai_akhir'));
                break;
            case 'belum':
                $query->whereDoesntHave('penilaian', fn ($q) => $q->whereNotNull('nilai_akhir'));
                break;
        }

        if (! empty($filters['huruf'])) {
            $query->whereHas('penilaian', fn ($q) => $q->where('nilai_huruf', $filters['huruf']));
        }

        return $query;
    }

    /**
     * Ringkasan statistik penilaian untuk sekumpulan mahasiswa.
     */
    public static function statistik(Builder $query): array
    {
        $mahasiswa = (clone $query)->with('penilaian', 'kelompok')->get();

        $total = $mahasiswa->count();
        $penilaian = $mahasiswa->pluck('penilaian')->filter();

        $lengkap = $penilaian->filter(fn (PenilaianPpl $p) => $p->nilai_akhir !== null);
        $mitraTerisi = $penilaian->filter(fn (PenilaianPpl $p) => $p->nilai_mitra !== null)->count();
        $dplTerisi = $penilaian->filter(fn (PenilaianPpl $p) => $p->nilai_dpl !== null)->count();
        $terkunci = $penilaian->filter(fn (PenilaianPpl $p) => $p->isLocked())->count();

        $rekapHuruf = array_fill_keys(self::HURUF_MUTU, 0);
        foreach ($lengkap as $p) {
            $rekapHuruf[$p->nilai_huruf] = ($rekapHuruf[$p->nilai_huruf] ?? 0) + 1;
        }

        $perKelompok = $mahasiswa->groupBy('kelompok_id');
        $kelompokSelesai = $perKelompok->filter(
            fn (Collection $anggota) => $anggota->every(fn ($m) => $m->penilaian?->nilai_akhir !== null)
        )->count();

        return [
            'total_mahasiswa' => $total,
            'mhs_lengkap' => $lengkap->count(),
            'mhs_belum' => $total - $lengkap->count(),
            'mitra_terisi' => $mitraTerisi,
            'dpl_terisi' => $dplTerisi,
            'terkunci' => $terkunci,
            'persen_lengkap' => $total > 0 ? round($lengkap->count() / $total * 100, 1) : 0,
            'rata_rata' => $lengkap->count() > 0 ? round($lengkap->avg('nilai_akhir'), 2) : 0,
            'rekap_huruf' => $rekapHuruf,
            'total_kelompok' => $perKelompok->count(),
            'kelompok_selesai' => $kelompokSelesai,
            'persen_kelompok' => $perKelompok->count() > 0 ? round($kelompokSelesai / $perKelompok->count() * 100, 1) : 0,
            'prodi' => [
                'Manajemen' => $mahasiswa->where('prodi', 'Manajemen')->count(),
                'Akuntansi' => $mahasiswa->where('prodi', 'Akuntansi')->count(),
                'Bisnis Digital' => $mahasiswa->where('prodi', 'Bisnis Digital')->count(),
            ],
        ];
    }

    public static function penilaianUntuk(AnggotaKelompok $mahasiswa): PenilaianPpl
    {
        $penilaian = $mahasiswa->penilaian ?? new PenilaianPpl([
            'anggota_kelompok_id' => $mahasiswa->id,
        ]);
        $penilaian->kelompok_id = $mahasiswa->kelompok_id;

        return $penilaian;
    }

    /**
     * Simpan nilai. $data dapat berisi nilai_mitra, catatan_mitra,
     * nilai_dpl, catatan_dpl, dan status (draft|locked).
     */
    public static function simpan(AnggotaKelompok $mahasiswa, array $data, User $oleh): PenilaianPpl
    {
        $penilaian = self::penilaianUntuk($mahasiswa);

        if ($penilaian->isLocked() && $oleh->role !== 'admin') {
            throw ValidationException::withMessages([
                'nilai' => "Nilai {$mahasiswa->nama} sudah dikunci (Final). Hubungi Admin untuk membuka kunci.",
            ]);
        }

        if (array_key_exists('nilai_mitra', $data)) {
            $penilaian->nilai_mitra = $data['nilai_mitra'] !== null ? (float) $data['nilai_mitra'] : null;
            if (array_key_exists('catatan_mitra', $data)) {
                $penilaian->catatan_mitra = $data['catatan_mitra'];
            }
            $penilaian->dinilai_mitra_at = $penilaian->nilai_mitra !== null ? now() : null;
        }

        if (array_key_exists('nilai_dpl', $data)) {
            $penilaian->nilai_dpl = $data['nilai_dpl'] !== null ? (float) $data['nilai_dpl'] : null;
            if (array_key_exists('catatan_dpl', $data)) {
                $penilaian->catatan_dpl = $data['catatan_dpl'];
            }
            $penilaian->dinilai_dpl_at = $penilaian->nilai_dpl !== null ? now() : null;
        }

        $status = $data['status'] ?? $penilaian->status;

        if ($status === PenilaianPpl::STATUS_LOCKED) {
            if (PenilaianPpl::hitungNilaiAkhir($penilaian->nilai_mitra, $penilaian->nilai_dpl) === null) {
                throw ValidationException::withMessages([
                    'status' => "Nilai {$mahasiswa->nama} belum bisa dikunci: Nilai Mitra dan Nilai Laporan DPL harus terisi keduanya.",
                ]);
            }

            if (! $penilaian->isLocked()) {
                $penilaian->locked_at = now();
                $penilaian->locked_by = $oleh->id;
            }
        } else {
            $penilaian->locked_at = null;
            $penilaian->locked_by = null;
        }

        $penilaian->status = $status;
        $penilaian->save();

        return $penilaian;
    }

    /**
     * Kunci nilai (Final). Hanya bisa jika Nilai Akhir sudah terbentuk.
     */
    public static function kunci(AnggotaKelompok $mahasiswa, User $oleh): bool
    {
        $penilaian = $mahasiswa->penilaian;

        if (! $penilaian || $penilaian->nilai_akhir === null || $penilaian->isLocked()) {
            return false;
        }

        $penilaian->update([
            'status' => PenilaianPpl::STATUS_LOCKED,
            'locked_at' => now(),
            'locked_by' => $oleh->id,
        ]);

        return true;
    }

    /**
     * Buka kunci nilai (khusus Admin) agar dapat direvisi kembali.
     */
    public static function bukaKunci(AnggotaKelompok $mahasiswa): bool
    {
        $penilaian = $mahasiswa->penilaian;

        if (! $penilaian || ! $penilaian->isLocked()) {
            return false;
        }

        $penilaian->update([
            'status' => PenilaianPpl::STATUS_DRAFT,
            'locked_at' => null,
            'locked_by' => null,
        ]);

        return true;
    }

    /**
     * Terapkan ulang konversi huruf mutu ke seluruh nilai (setelah skala diubah).
     */
    public static function hitungUlangHurufMutu(): void
    {
        $skala = PenilaianPpl::skalaNilaiHuruf();

        PenilaianPpl::whereNotNull('nilai_akhir')->each(function (PenilaianPpl $p) use ($skala) {
            $huruf = PenilaianPpl::konversiNilaiHuruf($p->nilai_akhir, $skala);
            if ($huruf !== $p->nilai_huruf) {
                $p->newQuery()->whereKey($p->id)->update(['nilai_huruf' => $huruf]);
            }
        });
    }

    /**
     * Apakah seluruh anggota kelompok sudah memiliki nilai pada kolom tertentu.
     */
    public static function kelompokSelesai(KelompokPpl $kelompok, string $kolom): bool
    {
        $anggota = $kelompok->anggota;

        return $anggota->isNotEmpty()
            && $anggota->every(fn ($m) => $m->penilaian?->{$kolom} !== null);
    }
}
