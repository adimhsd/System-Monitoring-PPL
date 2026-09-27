@extends('layouts.app')

@section('title', 'Detail Logbook Harian')

@section('content')
<div class="mb-4 d-flex align-items-center justify-content-between">
    <div>
        <a href="{{ route('ketua.logbook.index') }}" class="text-decoration-none text-secondary fs-7">&larr; Kembali ke Daftar Logbook</a>
        <h4 class="fw-bold mb-1 mt-1">Detail Logbook Kegiatan Harian PPL</h4>
    </div>
    <div>
        <a href="{{ route('ketua.logbook.edit', $logbook) }}" class="btn btn-outline-primary btn-touch rounded-3 fw-semibold fs-7 me-1">
            ✏️ Edit Logbook
        </a>
        <a href="{{ route('ketua.logbook.pdf') }}" class="btn btn-outline-danger btn-touch rounded-3 fw-semibold fs-7">
            📄 Cetak PDF
        </a>
    </div>
</div>

<div class="row g-3">
    <!-- Kolom Kiri: Deskripsi & Foto Dokumentasi -->
    <div class="col-12 col-md-8">
        <div class="card card-custom p-4 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-primary mb-0">📅 {{ $logbook->tanggal->translatedFormat('l, d F Y') }}</h5>
                <span class="badge bg-secondary fs-7">
                    🕒 {{ \Carbon\Carbon::parse($logbook->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($logbook->waktu_selesai)->format('H:i') }} WIB
                </span>
            </div>

            @if($logbook->terlambat)
                <div class="alert alert-warning py-2 px-3 fs-8 fw-semibold mb-3">
                    ⚠️ Pengisian Logbook Terlambat (>1 Hari dari Tanggal Kegiatan)
                </div>
            @endif

            <h6 class="fw-bold text-dark mb-2">Uraian & Deskripsi Kegiatan:</h6>
            <p class="text-secondary fs-7 mb-4 style-pre-line" style="white-space: pre-line;">{{ $logbook->deskripsi_kegiatan }}</p>

            <h6 class="fw-bold text-dark mb-2">Foto Dokumentasi:</h6>
            <div class="text-center p-3 bg-light rounded-3 mb-3 border">
                @if($logbook->foto_dokumentasi)
                    <img src="{{ route('logbook.foto', $logbook) }}" class="img-fluid rounded-3 shadow-sm" style="max-height: 420px; object-fit: contain;" alt="Foto Dokumentasi">
                @else
                    <span class="text-muted fs-7 italic">Tidak ada foto dokumentasi.</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Status Approval & Metadata -->
    <div class="col-12 col-md-4">
        <div class="card card-custom p-4 mb-3">
            <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">Informasi Kelompok & Approval</h6>

            <p class="text-secondary fs-7 mb-2">👥 <strong>Kelompok:</strong> {{ $logbook->kelompok->nama_kelompok }}</p>
            <p class="text-secondary fs-7 mb-2">🏢 <strong>Mitra:</strong> {{ $logbook->kelompok->mitra->nama_mitra ?? '-' }}</p>
            <p class="text-secondary fs-7 mb-2">👨‍💼 <strong>Pembimbing PIC:</strong> {{ $logbook->kelompok->mitra->picUser->nama_lengkap ?? '-' }}</p>
            <p class="text-secondary fs-7 mb-3">👨‍🏫 <strong>DPL Fakultas:</strong> {{ $logbook->kelompok->dpl->nama_lengkap ?? '-' }}</p>

            <hr>

            <h6 class="fw-bold text-dark fs-7 mb-2">Status Persetujuan Pembimbing:</h6>

            <div class="mb-3">
                <label class="form-label fs-8 text-muted fw-semibold mb-1">Status PIC Mitra:</label>
                @if($logbook->dilihat_mitra)
                    <div class="alert alert-success fs-8 py-2 px-3 mb-0">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong>✓ Approved PIC Mitra</strong>
                            @if(($logbook->status_validasi_mitra ?? 'sesuai') === 'tidak_sesuai')
                                <span class="badge bg-danger text-white fs-8">🔴 Tidak Sesuai</span>
                            @else
                                <span class="badge bg-success text-white fs-8">🟢 Sesuai</span>
                            @endif
                        </div>
                        @if($logbook->catatan_mitra)
                            <div class="mt-2 bg-white p-2 rounded border text-dark">
                                💬 <strong>Catatan PIC Mitra:</strong><br>
                                <em>"{{ $logbook->catatan_mitra }}"</em>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="alert alert-warning fs-8 py-2 px-3 mb-0">
                        ⏳ Menunggu approval Pembimbing PIC Mitra
                    </div>
                @endif
            </div>

            <div>
                <label class="form-label fs-8 text-muted fw-semibold mb-1">Status DPL Fakultas:</label>
                @if($logbook->dilihat_dpl)
                    <div class="alert alert-success fs-8 py-2 px-3 mb-0">
                        ✓ Telah di-approve oleh DPL pada {{ $logbook->dilihat_dpl_at ? $logbook->dilihat_dpl_at->translatedFormat('d/m/Y H:i') : '' }} WIB
                    </div>
                @else
                    <div class="alert alert-warning fs-8 py-2 px-3 mb-0">
                        ⏳ Menunggu approval Dosen Pembimbing (DPL)
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
