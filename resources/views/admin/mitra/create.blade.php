@extends('layouts.app')

@section('title', 'Tambah Mitra Baru')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.mitra.index') }}" class="text-decoration-none text-secondary fs-7">&larr; Kembali ke Master Mitra</a>
    <h4 class="fw-bold mb-1 mt-1">Tambah Mitra PPL Baru</h4>
</div>

<div class="card card-custom p-4 max-w-2xl mx-auto">
    <form action="{{ route('admin.mitra.store') }}" method="POST" autocomplete="off">
        @csrf

        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">🏢 Informasi Instansi / Perusahaan Mitra</h6>

        <div class="mb-3">
            <label for="nama_mitra" class="form-label fw-semibold text-secondary fs-7">Nama Mitra / Instansi / Perusahaan <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('nama_mitra') is-invalid @enderror" id="nama_mitra" name="nama_mitra" value="{{ old('nama_mitra') }}" placeholder="Contoh: BAPPEDA Kuningan" required>
            @error('nama_mitra')
                <div class="invalid-feedback fs-7">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="kategori" class="form-label fw-semibold text-secondary fs-7">Kategori Instansi / Mitra <span class="text-danger">*</span></label>
            <select class="form-select @error('kategori') is-invalid @enderror" id="kategori" name="kategori" required>
                <option value="">-- Pilih Kategori --</option>
                <option value="SKPD" {{ old('kategori') == 'SKPD' ? 'selected' : '' }}>SKPD (Instansi Pemda)</option>
                <option value="Swasta" {{ old('kategori') == 'Swasta' ? 'selected' : '' }}>Swasta / Perusahaan</option>
                <option value="UMKM" {{ old('kategori') == 'UMKM' ? 'selected' : '' }}>UMKM</option>
            </select>
            @error('kategori')
                <div class="invalid-feedback fs-7">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="alamat" class="form-label fw-semibold text-secondary fs-7">Alamat Lengkap Kantor</label>
            <textarea class="form-control" id="alamat" name="alamat" rows="3" placeholder="Masukkan alamat lengkap kantor/lokasi magang mitra">{{ old('alamat') }}</textarea>
        </div>

        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">👤 Informasi Akun Pembimbing / PIC Mitra</h6>

        <div class="p-3 bg-light rounded-3 mb-4 border">
            <div class="mb-3">
                <label for="pic_nama" class="form-label fs-7 fw-semibold text-secondary">Nama Lengkap PIC Mitra <span class="text-danger">*</span></label>
                <input type="text" id="pic_nama" name="pic_nama" class="form-control fs-7 @error('pic_nama') is-invalid @enderror" placeholder="Contoh: Haji Sobri, S.T." value="{{ old('pic_nama') }}" required>
                @error('pic_nama')
                    <div class="invalid-feedback fs-7">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="pic_username" class="form-label fs-7 fw-semibold text-secondary">Username Login PIC</label>
                <input type="text" id="pic_username" name="pic_username" class="form-control fs-7 @error('pic_username') is-invalid @enderror" placeholder="Contoh: pic_bappeda (Dibuat otomatis dari nama mitra jika kosong)" value="{{ old('pic_username') }}">
                @error('pic_username')
                    <div class="invalid-feedback fs-7">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="pic_password" class="form-label fs-7 fw-semibold text-secondary">Password Login PIC</label>
                <input type="password" id="pic_password" name="pic_password" class="form-control fs-7 @error('pic_password') is-invalid @enderror" placeholder="Kosongkan untuk menggunakan default (password123)">
                @error('pic_password')
                    <div class="invalid-feedback fs-7">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-1">
                <label for="pic_no_hp" class="form-label fs-7 fw-semibold text-secondary">No. HP / WhatsApp PIC</label>
                <input type="text" id="pic_no_hp" name="pic_no_hp" class="form-control fs-7" placeholder="Contoh: 085211223344" value="{{ old('pic_no_hp') }}">
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-touch text-white rounded-3 w-100 fw-semibold">
            Simpan Master Mitra & Akun PIC
        </button>
    </form>
</div>
@endsection
