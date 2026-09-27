@extends('layouts.app')

@section('title', 'Buat Akun Kelompok Baru')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.kelompok.index') }}" class="text-decoration-none text-secondary fs-7">&larr; Kembali ke Data Kelompok</a>
    <h4 class="fw-bold mb-1 mt-1">Buat Akun Kelompok PPL Baru</h4>
    <p class="text-muted mb-0 fs-7">Buat akun mandiri kelompok PPL yang dapat digunakan oleh seluruh anggota kelompok tersebut.</p>
</div>

<div class="row g-4 max-w-2xl mx-auto">
    <div class="col-12">
        <div class="card card-custom p-4">
            <form action="{{ route('admin.kelompok.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="nama_kelompok" class="form-label fw-semibold text-secondary fs-7">Nama Kelompok PPL <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nama_kelompok') is-invalid @enderror" id="nama_kelompok" name="nama_kelompok" value="{{ old('nama_kelompok') }}" required placeholder="Contoh: Kelompok 01 - BAPPEDA">
                    @error('nama_kelompok')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-sm-6">
                        <label for="username" class="form-label fw-semibold text-secondary fs-7">Username Login Akun <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username') }}" required placeholder="Contoh: kelompok_01">
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6">
                        <label for="password" class="form-label fw-semibold text-secondary fs-7">Password Login <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required placeholder="Minimal 6 karakter">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="tahun_akademik" class="form-label fw-semibold text-secondary fs-7">Tahun Akademik <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('tahun_akademik') is-invalid @enderror" id="tahun_akademik" name="tahun_akademik" value="{{ old('tahun_akademik', '2026/2027') }}" required>
                    @error('tahun_akademik')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6">
                        <label for="mitra_id" class="form-label fw-semibold text-secondary fs-7">Mitra Penempatan (Opsional)</label>
                        <select class="form-select @error('mitra_id') is-invalid @enderror" id="mitra_id" name="mitra_id">
                            <option value="">-- Pilih Nanti Saat Plotting --</option>
                            @foreach($mitraList as $m)
                                <option value="{{ $m->id }}" {{ old('mitra_id') == $m->id ? 'selected' : '' }}>{{ $m->nama_mitra }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-sm-6">
                        <label for="dpl_id" class="form-label fw-semibold text-secondary fs-7">DPL Pembimbing (Opsional)</label>
                        <select class="form-select @error('dpl_id') is-invalid @enderror" id="dpl_id" name="dpl_id">
                            <option value="">-- Pilih Nanti Saat Plotting --</option>
                            @foreach($dplList as $d)
                                <option value="{{ $d->id }}" {{ old('dpl_id') == $d->id ? 'selected' : '' }}>{{ $d->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-touch text-white rounded-3 w-100 fw-semibold">
                    Simpan & Buat Akun Kelompok
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
