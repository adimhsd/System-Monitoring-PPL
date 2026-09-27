<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DplImport implements ToModel, WithHeadingRow
{
    /**
     * Map baris Excel ke model User DPL.
     */
    public function model(array $row): Model|array|null
    {
        $namaLengkap = trim((string) ($row['nama_lengkap_dpl'] ?? $row['nama_lengkap'] ?? $row['nama'] ?? ''));
        $nipNidn = trim((string) ($row['nip_nidn'] ?? $row['nip'] ?? $row['nidn'] ?? ''));
        $username = trim((string) ($row['username'] ?? ''));

        // Jika baik nama, username, maupun nip_nidn kosong, lewati baris ini
        if (empty($namaLengkap) && empty($username) && empty($nipNidn)) {
            return null;
        }

        $noHp = isset($row['no_hp_whatsapp']) ? trim((string) $row['no_hp_whatsapp']) : (isset($row['no_hp']) ? trim((string) $row['no_hp']) : null);
        $email = isset($row['email']) ? trim((string) $row['email']) : null;
        $passwordInput = trim((string) ($row['password'] ?? 'password123'));
        if (empty($passwordInput)) {
            $passwordInput = 'password123';
        }

        $statusInput = strtolower(trim((string) ($row['status_akun'] ?? $row['status'] ?? 'aktif')));
        $isActive = !in_array($statusInput, ['non-aktif', 'nonaktif', '0', 'false', 'disabled', 'inaktif']);

        // 1. Cari user DPL yang sudah ada (termasuk soft-deleted): prioritaskan NIP/NIDN, kemudian username
        $user = null;
        if (!empty($nipNidn) && $nipNidn !== '-') {
            $user = User::withTrashed()->where('role', 'dpl')->where('nip_nidn', $nipNidn)->first();
        }

        if (!$user && !empty($username)) {
            $user = User::withTrashed()->where('role', 'dpl')->where('username', $username)->first();
        }

        // 2. Jika user DPL sudah ditemukan (aktif/soft-deleted), pulihkan jika perlu dan perbarui data secara aman
        if ($user) {
            if ($user->trashed()) {
                $user->restore();
            }

            $updatePayload = [
                'nama_lengkap' => !empty($namaLengkap) ? $namaLengkap : $user->nama_lengkap,
                'nip_nidn' => (!empty($nipNidn) && $nipNidn !== '-') ? $nipNidn : $user->nip_nidn,
                'no_hp' => (!empty($noHp) && $noHp !== '-') ? $noHp : $user->no_hp,
                'email' => (!empty($email) && $email !== '-') ? $email : $user->email,
                'is_active' => $isActive,
            ];

            // Hanya update username jika username di Excel diisi DAN username tersebut tidak dipakai oleh user lain di DB (termasuk trashed)
            if (!empty($username) && $username !== $user->username) {
                $isUsernameTaken = User::withTrashed()->where('username', $username)->where('id', '!=', $user->id)->exists();
                if (!$isUsernameTaken) {
                    $updatePayload['username'] = $username;
                }
            }

            $user->update($updatePayload);

            return $user;
        }

        // 3. Jika user DPL belum ada, siapkan pembuatan user baru
        if (empty($username)) {
            if (!empty($nipNidn) && $nipNidn !== '-') {
                $username = 'dpl_' . preg_replace('/[^a-zA-Z0-9]/', '', $nipNidn);
            } else {
                $username = 'dpl_' . Str::slug($namaLengkap, '_');
            }
        }

        if (empty($namaLengkap)) {
            $namaLengkap = 'DPL ' . strtoupper($username);
        }

        // Pastikan username unik di seluruh tabel users (termasuk soft-deleted) agar tidak terjadi SQL duplicate entry
        $originalUsername = $username;
        $counter = 1;
        while (User::withTrashed()->where('username', $username)->exists()) {
            $username = $originalUsername . '_' . $counter;
            $counter++;
        }

        return User::create([
            'username' => $username,
            'password' => Hash::make($passwordInput),
            'role' => 'dpl',
            'nama_lengkap' => $namaLengkap,
            'nip_nidn' => ($nipNidn && $nipNidn !== '-') ? $nipNidn : null,
            'no_hp' => ($noHp && $noHp !== '-') ? $noHp : null,
            'email' => ($email && $email !== '-') ? $email : null,
            'must_change_password' => true,
            'is_active' => $isActive,
        ]);
    }
}
