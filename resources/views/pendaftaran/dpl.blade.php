@extends('layouts.publik')

@section('title', 'Pendaftaran DPL PPL')
@section('heading', 'Formulir Pendaftaran Dosen Pembimbing Lapangan')
@section('subheading', 'DPL PPL Fakultas Ekonomi dan Bisnis — Universitas Kuningan')

@section('content')
@if(! $dibuka)
    <div class="alert alert-warning text-center mb-0">
        <strong>Pendaftaran DPL sedang ditutup.</strong><br>
        <span class="fs-7">Silakan hubungi Admin PPL FEB UNIKU untuk informasi lebih lanjut.</span>
    </div>
@else
    @if($errors->any())
        <div class="alert alert-danger fs-7">
            <strong>Periksa kembali isian Anda:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('pendaftaran.dpl.simpan') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-section-title mt-0">1. Data Dosen</div>
        <div class="row g-3">
            <div class="col-12">
                <label for="nama_lengkap" class="form-label fw-semibold fs-7">Nama Lengkap & Gelar <span class="text-danger">*</span></label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" maxlength="100" class="form-control @error('nama_lengkap') is-invalid @enderror" value="{{ old('nama_lengkap') }}" placeholder="Contoh: Dr. Ahmad Hidayat, S.E., M.M." required>
            </div>
            <div class="col-12 col-sm-6">
                <label for="nip_nidn" class="form-label fw-semibold fs-7">NIP / NIDN <span class="text-danger">*</span></label>
                <input type="text" id="nip_nidn" name="nip_nidn" maxlength="30" class="form-control @error('nip_nidn') is-invalid @enderror" value="{{ old('nip_nidn') }}" required>
            </div>
            <div class="col-12 col-sm-6">
                <label for="no_hp" class="form-label fw-semibold fs-7">No. HP / WhatsApp <span class="text-danger">*</span></label>
                <input type="tel" id="no_hp" name="no_hp" maxlength="20" class="form-control @error('no_hp') is-invalid @enderror" value="{{ old('no_hp') }}" placeholder="Contoh: 081234567890" required>
            </div>
            <div class="col-12">
                <label for="email" class="form-label fw-semibold fs-7">Email <span class="text-danger">*</span></label>
                <input type="email" id="email" name="email" maxlength="100" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="nama@uniku.ac.id" required>
            </div>
        </div>

        <div class="form-section-title">2. Surat Kesanggupan Menjadi DPL</div>
        <div class="alert alert-info fs-8 py-2">
            Gunakan <strong>template surat kesanggupan</strong> yang telah dibagikan Wakil Dekan melalui grup WhatsApp, isi & tandatangani, lalu unggah dalam format PDF.
        </div>
        <label for="surat_kesanggupan" class="form-label fw-semibold fs-7">Unggah Surat Kesanggupan (PDF, maks. 2 MB) <span class="text-danger">*</span></label>
        <input type="file" id="surat_kesanggupan" name="surat_kesanggupan" accept="application/pdf,.pdf" data-max-kb="2048"
               class="form-control file-pdf @error('surat_kesanggupan') is-invalid @enderror" required>
        <div class="invalid-feedback d-block fs-8 file-error"></div>

        <div class="form-check mt-4">
            <input class="form-check-input @error('pernyataan') is-invalid @enderror" type="checkbox" value="1" id="pernyataan" name="pernyataan" @checked(old('pernyataan')) required>
            <label class="form-check-label fs-7" for="pernyataan">
                Saya bersedia menjadi Dosen Pembimbing Lapangan (DPL) PPL FEB UNIKU dan data yang saya isikan adalah benar.
            </label>
        </div>

        <button type="submit" class="btn btn-primary btn-primary-custom text-white w-100 mt-4">📨 Kirim Pendaftaran DPL</button>
        <div class="text-center mt-3 fs-7">
            <a href="{{ route('pendaftaran.index') }}" class="text-decoration-none text-secondary">&larr; Kembali</a>
        </div>
    </form>
@endif
@endsection

@include('pendaftaran._cek-file')
