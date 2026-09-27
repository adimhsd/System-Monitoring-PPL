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
            <h5 class="fw-bold text-dark mb-3">Formulir Upload Luaran</h5>

            <form action="{{ route('ketua.luaran.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- File Laporan PDF (Max 10MB) -->
                <div class="mb-4">
                    <label for="file_laporan_pdf" class="form-label fw-semibold text-secondary fs-7">
                        File Laporan Akhir (Hanya Format PDF, Max 10MB)
                    </label>
                    <input type="file" class="form-control @error('file_laporan_pdf') is-invalid @enderror" id="file_laporan_pdf" name="file_laporan_pdf" accept="application/pdf" {{ $luaran ? '' : 'required' }}>
                    <div class="form-text fs-8 text-muted">Format file wajib <code>.pdf</code> dengan ukuran maksimal 10MB.</div>
                    @error('file_laporan_pdf') <div class="invalid-feedback fs-7">{{ $message }}</div> @enderror
                </div>

                <!-- Link Video YouTube -->
                <div class="mb-4">
                    <label for="url_video" class="form-label fw-semibold text-secondary fs-7">
                        Link URL Video Kegiatan (YouTube)
                    </label>
                    <input type="url" class="form-control @error('url_video') is-invalid @enderror" id="url_video" name="url_video" value="{{ old('url_video', $luaran->url_video ?? '') }}" placeholder="https://www.youtube.com/watch?v=..." required>
                    <div class="form-text fs-8 text-muted">Masukkan URL video dokumentasi/kegiatan yang telah diunggah ke YouTube.</div>
                    @error('url_video') <div class="invalid-feedback fs-7">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary btn-touch text-white rounded-3 w-100 fw-semibold">
                    {{ $luaran ? 'Perbarui Luaran Akhir' : 'Simpan & Unggah Luaran Akhir' }}
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
                    ⚠️ Kelompok Anda belum mengunggah luaran akhir PPL (Laporan PDF & Link Video YouTube).
                </div>
            @else
                <div class="alert alert-success fs-7 mb-3">
                    ✓ Luaran akhir telah berhasil diunggah pada <strong>{{ $luaran->uploaded_at?->translatedFormat('d F Y H:i') }} WIB</strong>.
                </div>

                <!-- Section PDF Report -->
                <div class="p-3 bg-light rounded-3 mb-3 border">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fs-8 text-muted d-block">Dokumen Laporan PDF:</span>
                            <span class="fw-semibold text-dark fs-7">Laporan_Akhir_PPL.pdf</span>
                        </div>
                        <a href="{{ route('luaran.pdf.download', $luaran) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            📥 Unduh PDF
                        </a>
                    </div>
                </div>

                <!-- Section Video YouTube -->
                <div class="p-3 bg-light rounded-3 border">
                    <span class="fs-8 text-muted d-block mb-1">Link Video Dokumentasi YouTube:</span>
                    <a href="{{ $luaran->url_video }}" target="_blank" class="fw-semibold text-primary fs-7 text-break d-block mb-2">
                        🎬 {{ $luaran->url_video }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
