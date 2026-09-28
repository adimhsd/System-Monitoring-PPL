@extends('layouts.publik')

@section('title', 'Pendaftaran Terkirim')
@section('heading', 'Pendaftaran Berhasil Dikirim')
@section('subheading', $data['jenis'])

@section('content')
<div class="text-center">
    <div style="font-size: 3.5rem;">✅</div>
    <h5 class="fw-bold mt-2">Terima kasih, {{ $data['nama'] }}!</h5>
    <p class="text-muted fs-7 mb-4">Data pendaftaran Anda ({{ $data['identitas'] }}) telah kami terima dan akan diverifikasi oleh Admin PPL FEB UNIKU.</p>

    <div class="d-inline-block border rounded-4 px-4 py-3 bg-light mb-4">
        <div class="text-muted fs-8 text-uppercase fw-bold">Nomor Pendaftaran</div>
        <div class="fs-3 fw-bold text-primary">{{ $data['nomor'] }}</div>
    </div>

    <p class="text-muted fs-8 mb-4">Simpan nomor pendaftaran ini. Informasi selanjutnya akan disampaikan melalui grup WhatsApp / kontak yang Anda daftarkan.</p>

    <a href="{{ route('pendaftaran.index') }}" class="btn btn-outline-primary rounded-3 px-4">Kembali ke Halaman Pendaftaran</a>
</div>
@endsection
