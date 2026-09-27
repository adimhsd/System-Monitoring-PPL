@extends('layouts.app')

@section('title', 'Tambah Akun Pengguna')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.users.index') }}" class="text-decoration-none text-secondary fs-7">&larr; Kembali ke Kelola User</a>
    <h4 class="fw-bold mb-1 mt-1">Tambah Akun Pengguna Baru</h4>
</div>

<div class="card card-custom p-4 max-w-2xl mx-auto">
    <form action="{{ route('admin.users.store') }}" method="POST" autocomplete="off">
        @csrf

        <div class="mb-3">
            <label for="nama_lengkap" class="form-label fw-semibold text-secondary fs-7">Nama Lengkap</label>
            <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required>
            @error('nama_lengkap') <div class="invalid-feedback fs-7">{{ $message }}</div> @enderror
        </div>

        <div class="row g-3 mb-3">
            <div class="col-12 col-md-6">
                <label for="username" class="form-label fw-semibold text-secondary fs-7">Username Login</label>
                <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username') }}" placeholder="Username unik" required>
                @error('username') <div class="invalid-feedback fs-7">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-md-6">
                <label for="role" class="form-label fw-semibold text-secondary fs-7">Role Pengguna</label>
                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                    <option value="">-- Pilih Role --</option>
                    <option value="admin" {{ old('role', $defaultRole ?? '') == 'admin' ? 'selected' : '' }}>Admin Unit PPL</option>
                    <option value="dpl" {{ old('role', $defaultRole ?? '') == 'dpl' ? 'selected' : '' }}>DPL (Dosen Pembimbing)</option>
                    <option value="pic_mitra" {{ old('role', $defaultRole ?? '') == 'pic_mitra' ? 'selected' : '' }}>PIC Mitra</option>
                    <option value="ketua_kelompok" {{ old('role', $defaultRole ?? '') == 'ketua_kelompok' ? 'selected' : '' }}>Akun Kelompok (Mahasiswa)</option>
                </select>
                @error('role') <div class="invalid-feedback fs-7">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-12 col-md-6">
                <label for="nip_nidn" class="form-label fw-semibold text-secondary fs-7">NIP / NIDN / NIM</label>
                <input type="text" class="form-control" id="nip_nidn" name="nip_nidn" value="{{ old('nip_nidn') }}" placeholder="Opsional">
            </div>

            <div class="col-12 col-md-6">
                <label for="no_hp" class="form-label fw-semibold text-secondary fs-7">No HP / Whatsapp</label>
                <input type="text" class="form-control" id="no_hp" name="no_hp" value="{{ old('no_hp') }}" placeholder="0812xxxx">
            </div>
        </div>

        <div class="mb-4">
            <label for="password" class="form-label fw-semibold text-secondary fs-7">Password Default</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" value="password" required>
            <div class="form-text fs-8 text-muted">Password default diset <code>password</code>. User wajib menggantinya saat login pertama.</div>
            @error('password') <div class="invalid-feedback fs-7">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-primary btn-touch text-white rounded-3 w-100 fw-semibold">
            Simpan Akun Baru
        </button>
    </form>
</div>
@endsection
