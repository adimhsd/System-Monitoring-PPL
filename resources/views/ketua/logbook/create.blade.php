@extends('layouts.app')

@section('title', 'Input Logbook Harian')

@section('content')
<div class="mb-4">
    <a href="{{ route('ketua.logbook.index') }}" class="text-decoration-none text-secondary fs-7">&larr; Kembali ke Daftar Logbook</a>
    <h4 class="fw-bold mb-1 mt-1">Input Logbook Harian PPL</h4>
</div>

<div class="card card-custom p-4 max-w-2xl mx-auto" x-data="logbookForm()">
    <form action="{{ route('ketua.logbook.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
        @csrf

        <div class="row g-3 mb-3">
            <div class="col-12 col-md-4">
                <label for="tanggal" class="form-label fw-semibold text-secondary fs-7">Tanggal Kegiatan</label>
                <input type="date" class="form-control @error('tanggal') is-invalid @enderror" id="tanggal" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                @error('tanggal') <div class="invalid-feedback fs-7">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-md-4">
                <label for="waktu_mulai" class="form-label fw-semibold text-secondary fs-7">Waktu Mulai</label>
                <input type="time" class="form-control @error('waktu_mulai') is-invalid @enderror" id="waktu_mulai" name="waktu_mulai" value="{{ old('waktu_mulai', '08:00') }}" required>
                @error('waktu_mulai') <div class="invalid-feedback fs-7">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-md-4">
                <label for="waktu_selesai" class="form-label fw-semibold text-secondary fs-7">Waktu Selesai</label>
                <input type="time" class="form-control @error('waktu_selesai') is-invalid @enderror" id="waktu_selesai" name="waktu_selesai" value="{{ old('waktu_selesai', '16:00') }}" required>
                @error('waktu_selesai') <div class="invalid-feedback fs-7">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="deskripsi_kegiatan" class="form-label fw-semibold text-secondary fs-7">Deskripsi Aktivitas & Uraian Kegiatan</label>
            <textarea class="form-control @error('deskripsi_kegiatan') is-invalid @enderror" id="deskripsi_kegiatan" name="deskripsi_kegiatan" rows="4" placeholder="Jelaskan secara rinci kegiatan magang yang dilakukan kelompok hari ini..." required>{{ old('deskripsi_kegiatan') }}</textarea>
            @error('deskripsi_kegiatan') <div class="invalid-feedback fs-7">{{ $message }}</div> @enderror
        </div>

        <div class="mb-4">
            <label for="foto_dokumentasi" class="form-label fw-semibold text-secondary fs-7">Unggah Foto Dokumentasi (Max 1MB)</label>
            <input type="file" class="form-control @error('foto_dokumentasi') is-invalid @enderror" id="foto_dokumentasi" name="foto_dokumentasi" accept="image/jpeg,image/png,image/webp" @change="previewImage($event)" required>
            <div class="form-text fs-8 text-muted">Format yang didukung: JPG, PNG, WebP. Maksimal 1MB di server.</div>
            @error('foto_dokumentasi') <div class="invalid-feedback fs-7">{{ $message }}</div> @enderror

            <!-- Live Image Preview -->
            <div class="mt-3" x-show="imageUrl">
                <p class="fs-8 fw-semibold text-secondary mb-1">Pratinjau Foto:</p>
                <img :src="imageUrl" class="img-thumbnail rounded-3" style="max-height: 200px; object-fit: cover;">
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-touch text-white rounded-3 w-100 fw-semibold">
            Simpan Logbook Harian
        </button>
    </form>
</div>

@push('scripts')
<script>
    function logbookForm() {
        return {
            imageUrl: null,
            previewImage(event) {
                const file = event.target.files[0];
                if (file) {
                    if (file.size > 1024 * 1024) {
                        alert('Ukuran file foto melebihi batas 1MB!');
                    }
                    this.imageUrl = URL.createObjectURL(file);
                }
            }
        };
    }
</script>
@endpush
@endsection
