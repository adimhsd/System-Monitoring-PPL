@extends('layouts.auth')

@section('title', 'Bantuan Reset Password')

@section('content')
<div class="auth-card mx-auto">
    <div class="auth-header">
        <div class="mb-2 text-center">
            <img src="{{ asset('images/logo-uniku.png') }}" alt="Logo Universitas Kuningan" style="height: 75px; width: auto; filter: drop-shadow(0 2px 6px rgba(0,0,0,0.2));">
        </div>
        <span class="brand-badge">Bantuan Reset Password</span>
        <h4 class="fw-bold mb-1">Lupa Password Akun?</h4>
        <p class="mb-0 text-white-50 fs-7">Sistem Monitoring PPL FEB UNIKU</p>
    </div>

    <div class="p-4 text-center">
        <div class="p-3 bg-light rounded-3 border mb-4 text-start fs-7">
            <p class="mb-2 fw-semibold text-dark">📋 Petunjuk Pemulihan Password:</p>
            <p class="text-muted mb-2">
                Untuk alasan keamanan dan verifikasi data, proses pemulihan atau reset password akun dilakukan secara langsung oleh <strong>Admin Unit PPL FEB UNIKU</strong>.
            </p>
            <p class="text-muted mb-0">
                Silakan hubungi Admin via WhatsApp dengan menyertakan <strong>Username / NIP / NIM</strong> serta <strong>Nama Lengkap</strong> Anda.
            </p>
        </div>

        <a href="https://wa.me/6285220621404?text=Halo%20Admin%20Unit%20PPL%20FEB%20UNIKU%2C%20saya%20membutuhkan%20bantuan%20untuk%20reset%20password%20akun%20Sistem%20Monitoring%20PPL.%20Mohon%20petunjuknya.%20Terima%20kasih." target="_blank" rel="noopener noreferrer" class="btn btn-success btn-touch w-100 fw-bold py-2 mb-3 shadow-sm rounded-3">
            💬 Hubungi Admin via WhatsApp (+6285220621404)
        </a>

        <div>
            <a href="{{ route('login') }}" class="text-decoration-none text-secondary fs-7 fw-semibold">
                &larr; Kembali ke Halaman Login
            </a>
        </div>
    </div>

    <div class="bg-light p-3 text-center border-top">
        <div class="fs-7 fw-semibold text-dark mb-1">
            &copy; {{ date('Y') }} FEB - Universitas Kuningan
        </div>
        <div class="text-muted" style="font-size: 0.65rem;">
            Developed by <a href="https://adi-muhamad.web.app/" target="_blank" rel="noopener noreferrer" class="text-decoration-none fw-semibold text-primary">Dosen Sontoloyo</a>
        </div>
    </div>
</div>
@endsection
