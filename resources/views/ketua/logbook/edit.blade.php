@extends('layouts.app')

@section('title', 'Edit Logbook Harian')

@section('content')
<div class="mb-4">
    <a href="{{ route('ketua.logbook.index') }}" class="text-decoration-none text-secondary fs-7">&larr; Kembali ke Daftar Logbook</a>
    <h4 class="fw-bold mb-1 mt-1">Edit Logbook Harian</h4>
</div>

<div class="card card-custom p-4 max-w-2xl mx-auto" x-data="editLogbookForm()">
    <form action="{{ route('ketua.logbook.update', $logbook) }}" method="POST" enctype="multipart/form-data" autocomplete="off">
        @csrf
        @method('PUT')

        <div class="row g-3 mb-3">
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold text-secondary fs-7">Tanggal Kegiatan</label>
                <input type="text" class="form-control bg-light" value="{{ $logbook->tanggal->translatedFormat('d F Y') }}" readonly>
            </div>

            <div class="col-12 col-md-4">
                <label for="waktu_mulai" class="form-label fw-semibold text-secondary fs-7">Waktu Mulai</label>
                <input type="time" class="form-control" id="waktu_mulai" name="waktu_mulai" value="{{ old('waktu_mulai', \Carbon\Carbon::parse($logbook->waktu_mulai)->format('H:i')) }}" required>
            </div>

            <div class="col-12 col-md-4">
                <label for="waktu_selesai" class="form-label fw-semibold text-secondary fs-7">Waktu Selesai</label>
                <input type="time" class="form-control" id="waktu_selesai" name="waktu_selesai" value="{{ old('waktu_selesai', \Carbon\Carbon::parse($logbook->waktu_selesai)->format('H:i')) }}" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="deskripsi_kegiatan" class="form-label fw-semibold text-secondary fs-7">Deskripsi Aktivitas & Uraian Kegiatan</label>
            <textarea class="form-control" id="deskripsi_kegiatan" name="deskripsi_kegiatan" rows="4" required>{{ old('deskripsi_kegiatan', $logbook->deskripsi_kegiatan) }}</textarea>
        </div>

        <div class="mb-4">
            <label for="foto_dokumentasi" class="form-label fw-semibold text-secondary fs-7">Ganti Foto Dokumentasi (Opsional, Max 1MB)</label>
            <input type="file" class="form-control" id="foto_dokumentasi" name="foto_dokumentasi" accept="image/jpeg,image/png,image/webp" @change="previewImage($event)">
            <div class="form-text fs-8 text-muted">Kosongkan jika tidak ingin mengubah foto dokumentasi.</div>

            <!-- Current / Preview Image -->
            <div class="mt-3">
                <p class="fs-8 fw-semibold text-secondary mb-1">Foto Saat Ini / Baru:</p>
                <img :src="imageUrl ? imageUrl : '{{ route('foto.show', $logbook) }}'" class="img-thumbnail rounded-3" style="max-height: 200px; object-fit: cover;">
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-touch text-white rounded-3 w-100 fw-semibold">
            Simpan Perubahan Logbook
        </button>
    </form>
</div>

@push('scripts')
<script>
    function editLogbookForm() {
        return {
            imageUrl: null,
            previewImage(event) {
                const file = event.target.files[0];
                if (file) {
                    this.imageUrl = URL.createObjectURL(file);
                }
            }
        };
    }
</script>
@endpush
@endsection
