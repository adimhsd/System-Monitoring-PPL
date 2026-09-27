<?php

namespace App\Services;

use App\Models\AnggotaKelompok;
use App\Models\ConfigAplikasi;
use App\Models\PendaftaranDpl;
use App\Models\PendaftaranMahasiswa;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pendaftaran publik mahasiswa PPL & calon DPL, serta verifikasi oleh Admin.
 */
class PendaftaranService
{
    public const PASSWORD_DEFAULT_DPL = 'FEB_Tangguh';

    public static function disk(): string
    {
        return config('filesystems.disks.r2.key') ? 'r2' : 'local';
    }

    /**
     * Status buka/tutup formulir (dikelola Admin). Default: dibuka.
     */
    public static function dibuka(string $jenis): bool
    {
        return (bool) ConfigAplikasi::get("pendaftaran_{$jenis}_dibuka", true);
    }

    public static function setDibuka(string $jenis, bool $dibuka): void
    {
        ConfigAplikasi::set("pendaftaran_{$jenis}_dibuka", $dibuka);
    }

    public static function simpanFile(UploadedFile $file, string $folder, string $nama): string
    {
        return $file->storeAs(
            'pendaftaran/' . $folder,
            Str::slug($nama, '_') . '_' . now()->format('YmdHis') . '_' . Str::random(6) . '.pdf',
            self::disk()
        );
    }

    public static function tampilkanFile(string $path, string $namaUnduhan): Response
    {
        $disk = self::disk();
        if (! Storage::disk($disk)->exists($path)) {
            $disk = 'local';
            abort_unless(Storage::disk($disk)->exists($path), 404, 'File tidak ditemukan.');
        }

        return response(Storage::disk($disk)->get($path), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $namaUnduhan . '"',
            'Cache-Control' => 'private, max-age=600',
        ]);
    }

    /**
     * Terima pendaftaran mahasiswa: buat / perbarui data master mahasiswa
     * (belum diplot ke kelompok, siap diproses di menu Plotting).
     */
    public static function terimaMahasiswa(PendaftaranMahasiswa $pendaftaran, User $admin): AnggotaKelompok
    {
        if ($pendaftaran->status !== 'menunggu') {
            throw ValidationException::withMessages(['status' => "Pendaftaran {$pendaftaran->nama} sudah diverifikasi sebelumnya."]);
        }

        return DB::transaction(function () use ($pendaftaran, $admin) {
            $mahasiswa = AnggotaKelompok::firstOrNew(['nim' => $pendaftaran->nim]);
            $mahasiswa->fill([
                'nama' => $pendaftaran->nama,
                'jenis_kelamin' => $pendaftaran->jenis_kelamin,
                'prodi' => $pendaftaran->prodi,
                'konsentrasi' => $pendaftaran->konsentrasi,
                'no_hp' => $pendaftaran->no_hp,
                'alamat' => $pendaftaran->alamat,
            ]);
            $mahasiswa->save();

            $pendaftaran->update([
                'status' => 'diterima',
                'anggota_kelompok_id' => $mahasiswa->id,
                'diverifikasi_oleh' => $admin->id,
                'diverifikasi_at' => now(),
            ]);

            return $mahasiswa;
        });
    }

    /**
     * Terima pendaftaran DPL: buat akun DPL dengan username DPL_PPLxx berikutnya.
     */
    public static function terimaDpl(PendaftaranDpl $pendaftaran, User $admin): User
    {
        if ($pendaftaran->status !== 'menunggu') {
            throw ValidationException::withMessages(['status' => "Pendaftaran {$pendaftaran->nama_lengkap} sudah diverifikasi sebelumnya."]);
        }

        return DB::transaction(function () use ($pendaftaran, $admin) {
            $user = User::where('role', 'dpl')->where('nip_nidn', $pendaftaran->nip_nidn)->first();

            if ($user) {
                $user->update([
                    'nama_lengkap' => $pendaftaran->nama_lengkap,
                    'no_hp' => $pendaftaran->no_hp,
                    'email' => $pendaftaran->email,
                    'is_active' => true,
                ]);
            } else {
                $user = User::create([
                    'username' => self::usernameDplBerikutnya(),
                    'password' => Hash::make(self::PASSWORD_DEFAULT_DPL),
                    'role' => 'dpl',
                    'nama_lengkap' => $pendaftaran->nama_lengkap,
                    'nip_nidn' => $pendaftaran->nip_nidn,
                    'no_hp' => $pendaftaran->no_hp,
                    'email' => $pendaftaran->email,
                    'must_change_password' => true,
                    'is_active' => true,
                ]);
            }

            $pendaftaran->update([
                'status' => 'diterima',
                'user_id' => $user->id,
                'diverifikasi_oleh' => $admin->id,
                'diverifikasi_at' => now(),
            ]);

            return $user;
        });
    }

    public static function tolak(PendaftaranMahasiswa|PendaftaranDpl $pendaftaran, User $admin, ?string $catatan): void
    {
        if ($pendaftaran->status !== 'menunggu') {
            throw ValidationException::withMessages(['status' => 'Pendaftaran ini sudah diverifikasi sebelumnya.']);
        }

        $pendaftaran->update([
            'status' => 'ditolak',
            'catatan_admin' => $catatan,
            'diverifikasi_oleh' => $admin->id,
            'diverifikasi_at' => now(),
        ]);
    }

    public static function usernameDplBerikutnya(): string
    {
        $maks = User::withTrashed()
            ->where('username', 'like', 'DPL_PPL%')
            ->pluck('username')
            ->map(fn ($u) => (int) preg_replace('/\D/', '', $u))
            ->max() ?? 0;

        return sprintf('DPL_PPL%02d', $maks + 1);
    }
}
