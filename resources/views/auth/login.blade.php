@extends('layouts.auth')

@section('title', 'Login Pengguna')

@section('content')
<div class="auth-card mx-auto">
    <div class="auth-header">
        <div class="mb-2 text-center">
            <img src="{{ asset('images/logo-uniku.png') }}" alt="Logo Universitas Kuningan" style="height: 75px; width: auto; filter: drop-shadow(0 2px 6px rgba(0,0,0,0.2));">
        </div>
        <span class="brand-badge">FEB UNIKU</span>
        <h4 class="fw-bold mb-1">Sistem Monitoring PPL</h4>
        <p class="mb-0 text-white-50 fs-7">Praktik Pengenalan Lapangan</p>
    </div>

    <div class="p-4">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show fs-7" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show fs-7" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('info'))
            <div class="alert alert-info alert-dismissible fade show fs-7" role="alert">
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show fs-7" role="alert">
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" autocomplete="off">
            @csrf

            <div class="mb-3">
                <label for="username" class="form-label fw-semibold text-secondary fs-7">Username</label>
                <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username') }}" placeholder="Masukkan username" required autofocus>
                @error('username')
                    <div class="invalid-feedback fs-7">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3" x-data="{ showPassword: false }">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="password" class="form-label fw-semibold text-secondary fs-7 mb-0">Password</label>
                    <a href="https://wa.me/6285220621404?text=Halo%20Admin%20Unit%20PPL%20FEB%20UNIKU%2C%20saya%20membutuhkan%20bantuan%20untuk%20reset%20password%20akun%20Sistem%20Monitoring%20PPL.%20Mohon%20petunjuknya.%20Terima%20kasih." target="_blank" rel="noopener noreferrer" class="text-decoration-none text-primary fs-7 fw-semibold">Lupa Password?</a>
                </div>
                <div class="input-group">
                    <input :type="showPassword ? 'text' : 'password'" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Masukkan password" required>
                    <button type="button" class="btn btn-outline-secondary border-start-0 d-flex align-items-center justify-content-center" @click="showPassword = !showPassword" tabindex="-1" style="width: 42px;" title="Lihat/Sembunyikan Password">
                        <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-eye text-muted" viewBox="0 0 16 16">
                            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-1.251 1.664-3.01 2.829-5.133 2.829s-3.882-1.165-5.133-2.829a13 13 0 0 1-.195-.288z"/>
                            <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                        </svg>
                        <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-eye-slash text-muted" viewBox="0 0 16 16">
                            <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.447-.73.876-1.18 1.274zM11.297 9.174a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299l.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/>
                            <path d="M3.35 5.47q-.27.242-.518.502A13 13 0 0 0 1.172 8s3 5.5 8 5.5a7 7 0 0 0 2.79-.588l-.77-.771A6 6 0 0 1 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8q.086-.13.195-.288c.335-.447.73-.876 1.18-1.274z"/>
                            <path d="M1.354 1.354a.5.5 0 0 1 .708 0l12 12a.5.5 0 0 1-.708.708l-12-12a.5.5 0 0 1 0-.708"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <div class="text-danger mt-1 fs-7">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label text-secondary fs-7" for="remember">
                    Ingat Saya di Perangkat Ini
                </label>
            </div>

            <button type="submit" class="btn btn-primary btn-primary-custom w-100 text-white">
                Masuk ke Sistem
            </button>
        </form>
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
