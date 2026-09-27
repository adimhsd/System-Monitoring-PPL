@extends('layouts.app')

@section('title', 'Edit Data Mitra')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.mitra.index') }}" class="text-decoration-none text-secondary fs-7">&larr; Kembali ke Master Mitra</a>
    <h4 class="fw-bold mb-1 mt-1">Edit Data Mitra & Akun PIC</h4>
</div>

<div class="card card-custom p-4 max-w-2xl mx-auto">
    <form action="{{ route('admin.mitra.update', $mitra) }}" method="POST" autocomplete="off">
        @csrf
        @method('PUT')

        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">🏢 Informasi Instansi / Perusahaan Mitra</h6>

        <div class="mb-3">
            <label for="nama_mitra" class="form-label fw-semibold text-secondary fs-7">Nama Mitra / Instansi / Perusahaan <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('nama_mitra') is-invalid @enderror" id="nama_mitra" name="nama_mitra" value="{{ old('nama_mitra', $mitra->nama_mitra) }}" required>
            @error('nama_mitra')
                <div class="invalid-feedback fs-7">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="kategori" class="form-label fw-semibold text-secondary fs-7">Kategori Instansi/Mitra <span class="text-danger">*</span></label>
            <select class="form-select @error('kategori') is-invalid @enderror" id="kategori" name="kategori" required>
                <option value="SKPD" {{ old('kategori', $mitra->kategori) == 'SKPD' ? 'selected' : '' }}>SKPD (Instansi Pemda)</option>
                <option value="Swasta" {{ old('kategori', $mitra->kategori) == 'Swasta' ? 'selected' : '' }}>Swasta / Perusahaan</option>
                <option value="UMKM" {{ old('kategori', $mitra->kategori) == 'UMKM' ? 'selected' : '' }}>UMKM</option>
            </select>
        </div>

        <div class="mb-4">
            <label for="alamat" class="form-label fw-semibold text-secondary fs-7">Alamat Lengkap Kantor</label>
            <textarea class="form-control" id="alamat" name="alamat" rows="3">{{ old('alamat', $mitra->alamat) }}</textarea>
        </div>

        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">👤 Informasi Akun Pembimbing / PIC Mitra</h6>

        <div class="p-3 bg-light rounded-3 mb-4 border">
            <div class="mb-3">
                <label for="pic_nama" class="form-label fs-7 fw-semibold text-secondary">Nama Lengkap PIC Mitra <span class="text-danger">*</span></label>
                <input type="text" id="pic_nama" name="pic_nama" class="form-control fs-7 @error('pic_nama') is-invalid @enderror" value="{{ old('pic_nama', $mitra->picUser->nama_lengkap ?? '') }}" required>
                @error('pic_nama')
                    <div class="invalid-feedback fs-7">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="pic_username" class="form-label fs-7 fw-semibold text-secondary">Username Login PIC <span class="text-danger">*</span></label>
                <input type="text" id="pic_username" name="pic_username" class="form-control fs-7 @error('pic_username') is-invalid @enderror" value="{{ old('pic_username', $mitra->picUser->username ?? '') }}" required>
                @error('pic_username')
                    <div class="invalid-feedback fs-7">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="pic_password" class="form-label fs-7 fw-semibold text-secondary">Ubah Password PIC (Opsional)</label>
                <input type="password" id="pic_password" name="pic_password" class="form-control fs-7 @error('pic_password') is-invalid @enderror" placeholder="Kosongkan jika tidak ingin mengubah password">
                @error('pic_password')
                    <div class="invalid-feedback fs-7">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-1">
                <label for="pic_no_hp" class="form-label fs-7 fw-semibold text-secondary">No. HP / WhatsApp PIC</label>
                <input type="text" id="pic_no_hp" name="pic_no_hp" class="form-control fs-7" value="{{ old('pic_no_hp', $mitra->picUser->no_hp ?? '') }}">
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-touch text-white rounded-3 w-100 fw-semibold">
            Simpan Perubahan Data Mitra & PIC
        </button>
    </form>
</div>
@endsection
