@extends('layouts.app')

@section('title', 'Edit Data DPL')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.dpl.index') }}" class="text-decoration-none text-secondary fs-7">&larr; Kembali ke Master Data DPL</a>
    <h4 class="fw-bold mb-1 mt-1">Edit Data DPL — {{ $dpl->nama_lengkap }}</h4>
    <p class="text-muted mb-0 fs-7">Perbarui kredensial akun dan data DPL.</p>
</div>

<div class="row g-4 max-w-2xl mx-auto">
    <div class="col-12">
        <div class="card card-custom p-4">
            <form action="{{ route('admin.dpl.update', $dpl) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3 mb-3">
                    <div class="col-12 col-sm-6">
                        <label for="username" class="form-label fw-semibold text-secondary fs-7">Username Login <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username', $dpl->username) }}" required>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6">
                        <label for="password" class="form-label fw-semibold text-secondary fs-7">Password Baru (Kosongkan jika tidak diubah)</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Min 6 karakter">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-sm-6">
                        <label for="nama_lengkap" class="form-label fw-semibold text-secondary fs-7">Nama Lengkap & Gelar <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $dpl->nama_lengkap) }}" required>
                        @error('nama_lengkap')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6">
                        <label for="nip_nidn" class="form-label fw-semibold text-secondary fs-7">NIP / NIDN</label>
                        <input type="text" class="form-control @error('nip_nidn') is-invalid @enderror" id="nip_nidn" name="nip_nidn" value="{{ old('nip_nidn', $dpl->nip_nidn) }}">
                        @error('nip_nidn')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-sm-6">
                        <label for="no_hp" class="form-label fw-semibold text-secondary fs-7">No. HP / Whatsapp</label>
                        <input type="text" class="form-control @error('no_hp') is-invalid @enderror" id="no_hp" name="no_hp" value="{{ old('no_hp', $dpl->no_hp) }}">
                        @error('no_hp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6">
                        <label for="email" class="form-label fw-semibold text-secondary fs-7">Alamat Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $dpl->email) }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label for="is_active" class="form-label fw-semibold text-secondary fs-7">Status Akun</label>
                    <select class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active" required>
                        <option value="1" {{ old('is_active', $dpl->is_active) ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ !old('is_active', $dpl->is_active) ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-touch text-white rounded-3 w-100 fw-semibold">
                    Simpan Perubahan Data DPL
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
