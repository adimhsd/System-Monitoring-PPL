@extends('layouts.app')

@section('title', 'Tambah Laporan Kunjungan Monitoring DPL')

@section('content')
<div class="mb-4">
    <a href="{{ route('dpl.monitoring.index') }}" class="text-decoration-none text-secondary fs-7">&larr; Kembali ke Daftar Monitoring DPL</a>
    <h4 class="fw-bold mb-1 mt-1">Tambah Laporan Kunjungan Lapangan DPL</h4>
    <p class="text-muted mb-0 fs-7">Unggah bukti foto dan catatan evaluasi kunjungan ke lokasi PPL Mitra (Penyerahan Awal / Penarikan Akhir / Kunjungan Rutin).</p>
</div>

<div class="row g-4 max-w-2xl mx-auto">
    <div class="col-12">
        <div class="card card-custom p-4">
            <form action="{{ route('dpl.monitoring.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Pilih Kelompok PPL -->
                <div class="mb-3">
                    <label for="kelompok_id" class="form-label fw-semibold text-secondary fs-7">Pilih Kelompok PPL Bimbingan <span class="text-danger">*</span></label>
                    <select name="kelompok_id" id="kelompok_id" class="form-select @error('kelompok_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Kelompok PPL --</option>
                        @foreach($kelompokList as $k)
                            <option value="{{ $k->id }}" {{ old('kelompok_id') == $k->id ? 'selected' : '' }}>
                                👥 {{ $k->nama_kelompok }} (Mitra: {{ $k->mitra->nama_mitra ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                    @error('kelompok_id')
                        <div class="invalid-feedback fs-7">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Jenis Kunjungan -->
                <div class="mb-3">
                    <label for="jenis_kunjungan" class="form-label fw-semibold text-secondary fs-7">Jenis Kunjungan Lapangan <span class="text-danger">*</span></label>
                    <select name="jenis_kunjungan" id="jenis_kunjungan" class="form-select @error('jenis_kunjungan') is-invalid @enderror" required>
                        <option value="penyerahan" {{ old('jenis_kunjungan') == 'penyerahan' ? 'selected' : '' }}>🚀 Kunjungan 1 - Penyerahan Mahasiswa (Awal PPL)</option>
                        <option value="penarikan" {{ old('jenis_kunjungan') == 'penarikan' ? 'selected' : '' }}>🏁 Kunjungan 2 - Penarikan Mahasiswa (Akhir PPL / Selesai 1 Bulan)</option>
                        <option value="kunjungan_rutin" {{ old('jenis_kunjungan') == 'kunjungan_rutin' ? 'selected' : '' }}>📍 Kunjungan Rutin / Evaluasi Tambahan</option>
                    </select>
                    @error('jenis_kunjungan')
                        <div class="invalid-feedback fs-7">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3 mb-3">
                    <!-- Tanggal Kunjungan -->
                    <div class="col-12 col-md-6">
                        <label for="tanggal_kunjungan" class="form-label fw-semibold text-secondary fs-7">Tanggal Kunjungan <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_kunjungan" id="tanggal_kunjungan" class="form-control @error('tanggal_kunjungan') is-invalid @enderror" value="{{ old('tanggal_kunjungan', date('Y-m-d')) }}" required>
                        @error('tanggal_kunjungan')
                            <div class="invalid-feedback fs-7">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Waktu Kunjungan -->
                    <div class="col-12 col-md-6">
                        <label for="waktu_kunjungan" class="form-label fw-semibold text-secondary fs-7">Waktu Jam Kunjungan</label>
                        <input type="time" name="waktu_kunjungan" id="waktu_kunjungan" class="form-control @error('waktu_kunjungan') is-invalid @enderror" value="{{ old('waktu_kunjungan', date('H:i')) }}">
                        @error('waktu_kunjungan')
                            <div class="invalid-feedback fs-7">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Catatan Evaluasi Kunjungan -->
                <div class="mb-3">
                    <label for="catatan_kunjungan" class="form-label fw-semibold text-secondary fs-7">Catatan & Hasil Evaluasi Kunjungan <span class="text-danger">*</span></label>
                    <textarea name="catatan_kunjungan" id="catatan_kunjungan" rows="4" class="form-control @error('catatan_kunjungan') is-invalid @enderror" placeholder="Tuliskan ringkasan kegiatan penyerahan/penarikan, koordinasi dengan mitra, serta hasil evaluasi bimbingan di lokasi..." required>{{ old('catatan_kunjungan') }}</textarea>
                    @error('catatan_kunjungan')
                        <div class="invalid-feedback fs-7">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Foto Dokumentasi Kunjungan -->
                <div class="mb-4" x-data="{ photoPreview: null }">
                    <label for="foto_kunjungan" class="form-label fw-semibold text-secondary fs-7">Foto Dokumentasi Kunjungan <span class="text-danger">*</span></label>
                    <input type="file" name="foto_kunjungan" id="foto_kunjungan" class="form-control @error('foto_kunjungan') is-invalid @enderror" accept="image/*" required @change="
                        const file = $event.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = (e) => { photoPreview = e.target.result; };
                            reader.readAsDataURL(file);
                        }
                    ">
                    <div class="form-text fs-8">Unggah foto kegiatan penyerahan/penarikan mahasiswa di lokasi mitra (Format: JPG, PNG, WebP, Maks 5MB).</div>
                    @error('foto_kunjungan')
                        <div class="invalid-feedback fs-7 d-block">{{ $message }}</div>
                    @enderror

                    <!-- Preview Image Box -->
                    <template x-if="photoPreview">
                        <div class="mt-3">
                            <span class="fs-8 text-muted d-block mb-1">Preview Foto Dokumentasi:</span>
                            <img :src="photoPreview" alt="Preview Foto" class="img-fluid rounded-3 border shadow-sm" style="max-height: 200px; object-fit: cover;">
                        </div>
                    </template>
                </div>

                <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                    <a href="{{ route('dpl.monitoring.index') }}" class="btn btn-outline-secondary btn-touch rounded-3">Batal</a>
                    <button type="submit" class="btn btn-primary btn-touch text-white rounded-3 fw-bold">
                        💾 Simpan & Unggah Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
