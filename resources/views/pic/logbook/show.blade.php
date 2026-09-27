@extends('layouts.app')

@section('title', 'Detail Logbook — PIC Mitra')

@section('content')
<div class="mb-4">
    <a href="{{ route('pic.logbook.index') }}" class="text-decoration-none text-secondary fs-7">&larr; Kembali ke Daftar Logbook</a>
    <h4 class="fw-bold mb-1 mt-1">Detail Logbook Harian PPL</h4>
</div>

<div class="row g-3">
    <div class="col-12 col-md-8">
        <div class="card card-custom p-4 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-primary mb-0">{{ $logbook->tanggal->translatedFormat('l, d F Y') }}</h5>
                <span class="badge bg-secondary fs-7">
                    🕒 {{ \Carbon\Carbon::parse($logbook->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($logbook->waktu_selesai)->format('H:i') }} WIB
                </span>
            </div>

            <h6 class="fw-bold text-dark mb-2">Uraian & Deskripsi Kegiatan:</h6>
            <p class="text-secondary fs-7 mb-4 style-pre-line" style="white-space: pre-line;">{{ $logbook->deskripsi_kegiatan }}</p>

            <h6 class="fw-bold text-dark mb-2">Foto Dokumentasi:</h6>
            <div class="text-center p-3 bg-light rounded-3 mb-3 border">
                <img src="{{ route('logbook.foto', $logbook) }}" class="img-fluid rounded-3 shadow-sm" style="max-height: 400px; object-fit: contain;" alt="Foto Dokumentasi">
            </div>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="card card-custom p-4 mb-3">
            <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">Informasi & Approval PIC Mitra</h6>

            <p class="text-secondary fs-7 mb-2">👥 <strong>Kelompok:</strong> {{ $logbook->kelompok->nama_kelompok }}</p>
            <p class="text-secondary fs-7 mb-2">👨‍🏫 <strong>DPL Fakultas:</strong> {{ $logbook->kelompok->dpl->nama_lengkap ?? '-' }}</p>
            <p class="text-secondary fs-7 mb-3">🔑 <strong>Akun Kelompok:</strong> {{ $logbook->kelompok->ketua->username ?? '-' }}</p>

            <hr>

            <h6 class="fw-bold text-dark fs-7 mb-3">Form Verification & Approval PIC Mitra:</h6>

            @if($logbook->dilihat_mitra)
                <div class="alert alert-success fs-7 mb-3 p-3 rounded-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <strong class="text-success">✓ Sudah Di-Approve</strong>
                        @if(($logbook->status_validasi_mitra ?? 'sesuai') === 'tidak_sesuai')
                            <span class="badge bg-danger text-white fs-8">🔴 Tidak Sesuai</span>
                        @else
                            <span class="badge bg-success text-white fs-8">🟢 Sesuai</span>
                        @endif
                    </div>
                    <span class="fs-8 text-muted d-block mb-2">Pada {{ $logbook->dilihat_mitra_at->translatedFormat('d F Y H:i') }} WIB</span>
                    
                    @if($logbook->catatan_mitra)
                        <div class="bg-white p-2 rounded-2 border fs-8 text-dark mt-2">
                            💬 <strong>Catatan PIC Mitra:</strong><br>
                            <em>"{{ $logbook->catatan_mitra }}"</em>
                        </div>
                    @endif
                </div>
            @else
                <div class="alert alert-warning fs-7 mb-3">
                    ⚠️ Logbook ini belum diverifikasi/di-approve.
                </div>
            @endif

            <form action="{{ route('pic.logbook.viewed', $logbook) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="status_validasi_mitra" class="form-label fw-bold text-dark fs-7 mb-1">
                        Keterangan Kesesuaian: <span class="text-danger">*</span>
                    </label>
                    <select name="status_validasi_mitra" id="status_validasi_mitra" class="form-select form-select-sm fs-7 fw-semibold @error('status_validasi_mitra') is-invalid @enderror" required>
                        <option value="sesuai" {{ old('status_validasi_mitra', $logbook->status_validasi_mitra ?? 'sesuai') === 'sesuai' ? 'selected' : '' }}>
                            🟢 Sesuai (Kegiatan dilaporkan valid)
                        </option>
                        <option value="tidak_sesuai" {{ old('status_validasi_mitra', $logbook->status_validasi_mitra ?? '') === 'tidak_sesuai' ? 'selected' : '' }}>
                            🔴 Tidak Sesuai (Perlu perbaikan)
                        </option>
                    </select>
                    @error('status_validasi_mitra')
                        <div class="invalid-feedback fs-8">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="catatan_mitra" class="form-label fw-bold text-dark fs-7 mb-1">
                        Catatan / Umpan Balik (Opsional):
                    </label>
                    <textarea name="catatan_mitra" id="catatan_mitra" class="form-control form-control-sm fs-7" rows="3" placeholder="Masukkan catatan atau masukan jika tidak sesuai...">{{ old('catatan_mitra', $logbook->catatan_mitra) }}</textarea>
                </div>

                <button type="submit" class="btn btn-success btn-touch text-white w-100 fw-semibold rounded-3 shadow-sm">
                    {{ $logbook->dilihat_mitra ? '🔄 Perbarui Approval & Keterangan' : '✓ Simpan Approval Logbook' }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
