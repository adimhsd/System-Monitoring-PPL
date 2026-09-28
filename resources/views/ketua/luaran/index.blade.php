@extends('layouts.app')

@section('title', 'Luaran Akhir PPL')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Unggah Luaran Akhir PPL</h4>
    <p class="text-muted mb-0 fs-7">Kelompok: <strong>{{ $kelompok->nama_kelompok }}</strong> (Mitra: {{ $kelompok->mitra->nama_mitra }})</p>
</div>

<div class="row g-4">
    <!-- Form Upload Luaran -->
    <div class="col-12 col-md-6">
        <div class="card card-custom p-4">
            <h5 class="fw-bold text-dark mb-3">Formulir 3 Luaran Akhir Kegiatan</h5>

            <form action="{{ route('ketua.luaran.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- 1. File Laporan PDF (Max 5MB) -->
                <div class="mb-3">
                    <label for="file_laporan_pdf" class="form-label fw-semibold text-secondary fs-7">
                        1. Laporan Akhir Kegiatan (Format PDF, Maksimal 5MB)
                    </label>
                    <input type="file" class="form-control @error('file_laporan_pdf') is-invalid @enderror" id="file_laporan_pdf" name="file_laporan_pdf" accept="application/pdf" {{ $luaran && $luaran->file_laporan_pdf ? '' : 'required' }}>
                    <div class="form-text fs-8 text-muted">Format file wajib <code>.pdf</code> dengan ukuran maksimal 5MB.</div>
                    @error('file_laporan_pdf') <div class="invalid-feedback fs-7">{{ $message }}</div> @enderror
                </div>

                <!-- 2. Link Video YouTube -->
                <div class="mb-3">
                    <label for="url_video" class="form-label fw-semibold text-secondary fs-7">
                        2. Link URL Video Dokumentasi Kegiatan (YouTube)
                    </label>
                    <input type="url" class="form-control @error('url_video') is-invalid @enderror" id="url_video" name="url_video" value="{{ old('url_video', $luaran->url_video ?? '') }}" placeholder="https://www.youtube.com/watch?v=..." required>
                    <div class="form-text fs-8 text-muted">Masukkan link URL video dokumentasi yang sudah dipublikasikan di YouTube.</div>
                    @error('url_video') <div class="invalid-feedback fs-7">{{ $message }}</div> @enderror
                </div>

                <!-- 3. Poster Kegiatan (Max 3MB) -->
                <div class="mb-4">
                    <label for="file_poster" class="form-label fw-semibold text-secondary fs-7">
                        3. Poster Kegiatan Resolusi Tinggi (Format Gambar, Maksimal 3MB)
                    </label>
                    <input type="file" class="form-control @error('file_poster') is-invalid @enderror" id="file_poster" name="file_poster" accept="image/jpeg,image/png,image/webp" {{ $luaran && $luaran->file_poster ? '' : 'required' }}>
                    <div class="form-text fs-8 text-muted">Format gambar: <code>JPG, PNG, atau WebP</code> (resolusi tinggi, maks. 3MB).</div>
                    @error('file_poster') <div class="invalid-feedback fs-7">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary btn-touch text-white rounded-3 w-100 fw-semibold">
                    {{ $luaran ? 'Perbarui Luaran Akhir' : 'Simpan & Unggah 3 Luaran Akhir' }}
                </button>
            </form>
        </div>
    </div>

    <!-- Pratinjau / Status Luaran Akhir -->
    <div class="col-12 col-md-6">
        <div class="card card-custom p-4 h-100">
            <h5 class="fw-bold text-dark mb-3">Status & Pratinjau Luaran</h5>

            @if(!$luaran)
                <div class="alert alert-warning fs-7 mb-0">
                    ⚠️ Kelompok Anda belum mengunggah luaran akhir PPL (Laporan PDF, Video YouTube, & Poster Kegiatan).
                </div>
            @else
                <div class="alert alert-success fs-7 mb-3">
                    ✓ Luaran akhir telah berhasil diunggah pada <strong>{{ $luaran->uploaded_at?->translatedFormat('d F Y H:i') }} WIB</strong>.
                </div>

                <!-- 1. Section PDF Report -->
                <div class="p-3 bg-light rounded-3 mb-3 border">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fs-8 text-muted d-block">1. Dokumen Laporan PDF (Maks. 5MB):</span>
                            @if($luaran->file_laporan_pdf)
                                <span class="fw-semibold text-success fs-7">✓ File Laporan Tersedia</span>
                            @else
                                <span class="text-danger fs-7">Belum Diunggah</span>
                            @endif
                        </div>
                        @if($luaran->file_laporan_pdf)
                            <a href="{{ route('luaran.pdf.download', $luaran) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                📥 Unduh PDF
                            </a>
                        @endif
                    </div>
                </div>

                <!-- 2. Section Video YouTube -->
                <div class="p-3 bg-light rounded-3 mb-3 border">
                    <span class="fs-8 text-muted d-block mb-1">2. Link Video Dokumentasi YouTube:</span>
                    @if($luaran->url_video)
                        <a href="{{ $luaran->url_video }}" target="_blank" class="fw-semibold text-danger fs-7 text-break d-inline-flex align-items-center gap-1">
                            <span>🎬</span> <span>{{ $luaran->url_video }}</span>
                        </a>
                    @else
                        <span class="text-danger fs-7">Belum Diisi</span>
                    @endif
                </div>

                <!-- 3. Section Poster Kegiatan -->
                <div class="p-3 bg-light rounded-3 border">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fs-8 text-muted d-block">3. Poster Kegiatan (Resolusi Tinggi, Maks. 3MB):</span>
                        @if($luaran->file_poster)
                            <a href="{{ route('luaran.poster.show', $luaran) }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-3">
                                🖼️ Lihat Resolusi Penuh
                            </a>
                        @endif
                    </div>
                    @if($luaran->file_poster)
                        <div class="text-center bg-white p-2 rounded border">
                            <img src="{{ route('luaran.poster.show', $luaran) }}" alt="Poster Kegiatan" class="img-fluid rounded" style="max-height: 220px; object-fit: contain;">
                        </div>
                    @else
                        <span class="text-danger fs-7">Belum Diunggah</span>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
