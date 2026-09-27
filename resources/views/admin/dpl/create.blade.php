@extends('layouts.app')

@section('title', 'Tambah DPL Baru')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.dpl.index') }}" class="text-decoration-none text-secondary fs-7">&larr; Kembali ke Master Data DPL</a>
    <h4 class="fw-bold mb-1 mt-1">Tambah Data DPL Baru</h4>
    <p class="text-muted mb-0 fs-7">Buat akun Dosen Pembimbing Lapangan (DPL) baru.</p>
</div>

<div class="row g-4 max-w-2xl mx-auto">
    <div class="col-12">
        <div class="card card-custom p-4">
            <form action="{{ route('admin.dpl.store') }}" method="POST">
                @csrf

                <div class="row g-3 mb-3">
                    <div class="col-12 col-sm-6">
                        <label for="username" class="form-label fw-semibold text-secondary fs-7">Username Login <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username') }}" required placeholder="Contoh: dpl_haji_asep">
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6">
                        <label for="password" class="form-label fw-semibold text-secondary fs-7">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required placeholder="Minimal 6 karakter">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-sm-6">
                        <label for="nama_lengkap" class="form-label fw-semibold text-secondary fs-7">Nama Lengkap & Gelar <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required placeholder="Contoh: Dr. H. Asep Sunandar, M.Si.">
                        @error('nama_lengkap')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6">
                        <label for="nip_nidn" class="form-label fw-semibold text-secondary fs-7">NIP / NIDN</label>
                        <input type="text" class="form-control @error('nip_nidn') is-invalid @enderror" id="nip_nidn" name="nip_nidn" value="{{ old('nip_nidn') }}" placeholder="Contoh: 0412058001">
                        @error('nip_nidn')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6">
                        <label for="no_hp" class="form-label fw-semibold text-secondary fs-7">No. HP / Whatsapp</label>
                        <input type="text" class="form-control @error('no_hp') is-invalid @enderror" id="no_hp" name="no_hp" value="{{ old('no_hp') }}" placeholder="Contoh: 081298765432">
                        @error('no_hp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6">
                        <label for="email" class="form-label fw-semibold text-secondary fs-7">Alamat Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Contoh: asep@uniku.ac.id">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-touch text-white rounded-3 w-100 fw-semibold">
                    Simpan & Buat Akun DPL
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
