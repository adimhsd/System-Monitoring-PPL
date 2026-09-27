@extends('layouts.publik')

@section('title', 'Pendaftaran PPL')
@section('heading', 'Pendaftaran PPL FEB UNIKU')
@section('subheading', 'Pilih formulir pendaftaran sesuai peran Anda')

@section('content')
<div class="row g-3">
    <div class="col-12 col-md-6">
        <div class="border rounded-4 p-4 h-100 d-flex flex-column">
            <div class="fs-2 mb-2">👨‍🎓</div>
            <h5 class="fw-bold mb-1">Mahasiswa PPL</h5>
            <p class="text-muted fs-7 flex-grow-1">Pendaftaran peserta PPL <strong>Reguler</strong> maupun <strong>Rekognisi MBKM</strong>. Siapkan bukti pembayaran dalam format PDF (maks. 1 MB).</p>
            @if($mahasiswaDibuka)
                <a href="{{ route('pendaftaran.mahasiswa') }}" class="btn btn-primary btn-primary-custom text-white">Isi Formulir Mahasiswa</a>
            @else
                <button class="btn btn-secondary" disabled>Pendaftaran Ditutup</button>
            @endif
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="border rounded-4 p-4 h-100 d-flex flex-column">
            <div class="fs-2 mb-2">👨‍🏫</div>
            <h5 class="fw-bold mb-1">Dosen Pembimbing Lapangan</h5>
            <p class="text-muted fs-7 flex-grow-1">Pendaftaran calon DPL PPL. Siapkan <strong>surat kesanggupan menjadi DPL</strong> yang sudah ditandatangani dalam format PDF (maks. 2 MB).</p>
            @if($dplDibuka)
                <a href="{{ route('pendaftaran.dpl') }}" class="btn btn-primary btn-primary-custom text-white">Isi Formulir DPL</a>
            @else
                <button class="btn btn-secondary" disabled>Pendaftaran Ditutup</button>
            @endif
        </div>
    </div>
</div>
<div class="text-center mt-4 fs-7">
    <a href="{{ route('login') }}" class="text-decoration-none">&larr; Masuk ke Sistem Monitoring PPL</a>
</div>
@endsection
