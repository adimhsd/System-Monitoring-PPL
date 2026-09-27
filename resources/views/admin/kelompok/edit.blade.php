@extends('layouts.app')

@section('title', 'Edit Akun & Kredensial Kelompok')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.kelompok.index') }}" class="text-decoration-none text-secondary fs-7">&larr; Kembali ke Data Kelompok</a>
    <h4 class="fw-bold mb-1 mt-1">Edit Kredensial Akun — {{ $kelompok->nama_kelompok }}</h4>
    <p class="text-muted mb-0 fs-7">Ubah nama kelompok, username, password login, atau status aktif kelompok.</p>
</div>

<div class="row g-4 max-w-2xl mx-auto">
    <div class="col-12">
        <div class="card card-custom p-4">
            <form action="{{ route('admin.kelompok.update', $kelompok) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="nama_kelompok" class="form-label fw-semibold text-secondary fs-7">Nama Kelompok PPL <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nama_kelompok') is-invalid @enderror" id="nama_kelompok" name="nama_kelompok" value="{{ old('nama_kelompok', $kelompok->nama_kelompok) }}" required>
                    @error('nama_kelompok')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-sm-6">
                        <label for="username" class="form-label fw-semibold text-secondary fs-7">Username Login <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username', $kelompok->ketua->username ?? '') }}" required>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6">
                        <label for="password" class="form-label fw-semibold text-secondary fs-7">Reset Password (Opsional)</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Isi jika ingin ubah password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6">
                        <label for="tahun_akademik" class="form-label fw-semibold text-secondary fs-7">Tahun Akademik <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('tahun_akademik') is-invalid @enderror" id="tahun_akademik" name="tahun_akademik" value="{{ old('tahun_akademik', $kelompok->tahun_akademik) }}" required>
                        @error('tahun_akademik')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6">
                        <label for="status" class="form-label fw-semibold text-secondary fs-7">Status Kelompok <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="aktif" {{ old('status', $kelompok->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="selesai" {{ old('status', $kelompok->status) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="dibatalkan" {{ old('status', $kelompok->status) == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-touch text-white rounded-3 w-100 fw-semibold">
                    Simpan Perubahan Kredensial Akun
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
