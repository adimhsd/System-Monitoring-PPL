@extends('layouts.app')

@section('title', 'Edit Akun Pengguna')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.users.index') }}" class="text-decoration-none text-secondary fs-7">&larr; Kembali ke Kelola User</a>
    <h4 class="fw-bold mb-1 mt-1">Edit Akun Pengguna</h4>
</div>

<div class="card card-custom p-4 max-w-2xl mx-auto">
    <form action="{{ route('admin.users.update', $user) }}" method="POST" autocomplete="off">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nama_lengkap" class="form-label fw-semibold text-secondary fs-7">Nama Lengkap</label>
            <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $user->nama_lengkap) }}" required>
            @error('nama_lengkap') <div class="invalid-feedback fs-7">{{ $message }}</div> @enderror
        </div>

        <div class="row g-3 mb-3">
            <div class="col-12 col-md-6">
                <label for="username" class="form-label fw-semibold text-secondary fs-7">Username Login</label>
                <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username', $user->username) }}" required>
                @error('username') <div class="invalid-feedback fs-7">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-md-6">
                <label for="role" class="form-label fw-semibold text-secondary fs-7">Role Pengguna</label>
                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin Unit PPL</option>
                    <option value="dpl" {{ old('role', $user->role) == 'dpl' ? 'selected' : '' }}>DPL (Dosen Pembimbing)</option>
                    <option value="pic_mitra" {{ old('role', $user->role) == 'pic_mitra' ? 'selected' : '' }}>PIC Mitra</option>
                    <option value="ketua_kelompok" {{ old('role', $user->role) == 'ketua_kelompok' ? 'selected' : '' }}>Ketua Kelompok (Mahasiswa)</option>
                </select>
                @error('role') <div class="invalid-feedback fs-7">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-12 col-md-6">
                <label for="nip_nidn" class="form-label fw-semibold text-secondary fs-7">NIP / NIDN / NIM</label>
                <input type="text" class="form-control" id="nip_nidn" name="nip_nidn" value="{{ old('nip_nidn', $user->nip_nidn) }}">
            </div>

            <div class="col-12 col-md-6">
                <label for="no_hp" class="form-label fw-semibold text-secondary fs-7">No HP / Whatsapp</label>
                <input type="text" class="form-control" id="no_hp" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}">
            </div>
        </div>

        <div class="mb-3">
            <label for="is_active" class="form-label fw-semibold text-secondary fs-7">Status Akun</label>
            <select class="form-select" id="is_active" name="is_active" required>
                <option value="1" {{ old('is_active', $user->is_active) ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ !old('is_active', $user->is_active) ? 'selected' : '' }}>Non-Aktif</option>
            </select>
        </div>

        <div class="mb-4 p-3 bg-light rounded-3 border">
            <label for="new_password" class="form-label fw-semibold text-secondary fs-7 mb-1">Reset Password (Opsional)</label>
            <input type="password" class="form-control fs-7" id="new_password" name="new_password" placeholder="Isi hanya jika ingin mereset password pengguna">
            <div class="form-text fs-8 text-muted mt-1">Jika diisi, user akan diwajibkan mengganti password saat login berikutnya.</div>
        </div>

        <button type="submit" class="btn btn-primary btn-touch text-white rounded-3 w-100 fw-semibold">
            Simpan Perubahan Akun
        </button>
    </form>
</div>
@endsection
