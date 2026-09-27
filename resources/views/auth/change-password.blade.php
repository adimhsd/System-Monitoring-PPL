@extends('layouts.auth')

@section('title', 'Ubah Password Akun')

@section('content')
<div class="auth-card mx-auto">
    <div class="auth-header">
        <span class="brand-badge bg-warning text-dark">Keamanan Akun</span>
        <h4 class="fw-bold mb-1">Perbarui Password Akun</h4>
        <p class="mb-0 text-white-50 fs-7">
            @if(Auth::user()->must_change_password)
                Wajib ganti password default saat login pertama demi keamanan
            @else
                Perbarui password akun Anda secara berkala
            @endif
        </p>
    </div>

    <div class="p-4" x-data="{ showCurrent: false, showNew: false, showConfirm: false }">
        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show fs-7" role="alert">
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('info'))
            <div class="alert alert-info alert-dismissible fade show fs-7" role="alert">
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Kotak Petunjuk Format Password Bahasa Indonesia -->
        <div class="p-3 bg-light rounded-3 mb-4 border fs-8 text-secondary">
            <strong class="d-block mb-1 text-dark">📋 Ketentuan Format Password Baru:</strong>
            <ul class="mb-0 ps-3">
                <li>Panjang password <strong>minimal 8 karakter</strong>.</li>
                <li>Disarankan mengombinasikan <strong>huruf kapital (A-Z)</strong>, <strong>huruf kecil (a-z)</strong>, dan <strong>angka (0-9)</strong>.</li>
                <li>Pastikan <strong>Password Baru</strong> dan <strong>Konfirmasi Password Baru</strong> diisi sama persis.</li>
            </ul>
        </div>

        <form action="{{ route('password.change.update') }}" method="POST" autocomplete="off">
            @csrf

            <!-- Password Saat Ini -->
            <div class="mb-3">
                <label for="current_password" class="form-label fw-semibold text-secondary fs-7">Password Saat Ini</label>
                <div class="input-group">
                    <input :type="showCurrent ? 'text' : 'password'" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" placeholder="Masukkan password saat ini" required autofocus>
                    <button type="button" class="btn btn-outline-secondary border-start-0 d-flex align-items-center justify-content-center" @click="showCurrent = !showCurrent" tabindex="-1" style="width: 42px;" title="Lihat/Sembunyikan Password">
                        <svg x-show="!showCurrent" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-eye text-muted" viewBox="0 0 16 16">
                            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-1.251 1.664-3.01 2.829-5.133 2.829s-3.882-1.165-5.133-2.829a13 13 0 0 1-.195-.288z"/>
                            <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                        </svg>
                        <svg x-show="showCurrent" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-eye-slash text-muted" viewBox="0 0 16 16">
                            <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.447-.73.876-1.18 1.274zM11.297 9.174a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299l.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/>
                            <path d="M3.35 5.47q-.27.242-.518.502A13 13 0 0 0 1.172 8s3 5.5 8 5.5a7 7 0 0 0 2.79-.588l-.77-.771A6 6 0 0 1 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8q.086-.13.195-.288c.335-.447.73-.876 1.18-1.274z"/>
                            <path d="M1.354 1.354a.5.5 0 0 1 .708 0l12 12a.5.5 0 0 1-.708.708l-12-12a.5.5 0 0 1 0-.708"/>
                        </svg>
                    </button>
                </div>
                @error('current_password')
                    <div class="text-danger mt-1 fs-7">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Password Baru -->
            <div class="mb-3">
                <label for="password" class="form-label fw-semibold text-secondary fs-7">Password Baru</label>
                <div class="input-group">
                    <input :type="showNew ? 'text' : 'password'" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Minimal 8 karakter" required>
                    <button type="button" class="btn btn-outline-secondary border-start-0 d-flex align-items-center justify-content-center" @click="showNew = !showNew" tabindex="-1" style="width: 42px;" title="Lihat/Sembunyikan Password">
                        <svg x-show="!showNew" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-eye text-muted" viewBox="0 0 16 16">
                            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-1.251 1.664-3.01 2.829-5.133 2.829s-3.882-1.165-5.133-2.829a13 13 0 0 1-.195-.288z"/>
                            <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                        </svg>
                        <svg x-show="showNew" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-eye-slash text-muted" viewBox="0 0 16 16">
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

            <!-- Konfirmasi Password Baru -->
            <div class="mb-4">
                <label for="password_confirmation" class="form-label fw-semibold text-secondary fs-7">Konfirmasi Password Baru</label>
                <div class="input-group">
                    <input :type="showConfirm ? 'text' : 'password'" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password baru" required>
                    <button type="button" class="btn btn-outline-secondary border-start-0 d-flex align-items-center justify-content-center" @click="showConfirm = !showConfirm" tabindex="-1" style="width: 42px;" title="Lihat/Sembunyikan Password">
                        <svg x-show="!showConfirm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-eye text-muted" viewBox="0 0 16 16">
                            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-1.251 1.664-3.01 2.829-5.133 2.829s-3.882-1.165-5.133-2.829a13 13 0 0 1-.195-.288z"/>
                            <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                        </svg>
                        <svg x-show="showConfirm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-eye-slash text-muted" viewBox="0 0 16 16">
                            <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.447-.73.876-1.18 1.274zM11.297 9.174a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299l.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/>
                            <path d="M3.35 5.47q-.27.242-.518.502A13 13 0 0 0 1.172 8s3 5.5 8 5.5a7 7 0 0 0 2.79-.588l-.77-.771A6 6 0 0 1 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8q.086-.13.195-.288c.335-.447.73-.876 1.18-1.274z"/>
                            <path d="M1.354 1.354a.5.5 0 0 1 .708 0l12 12a.5.5 0 0 1-.708.708l-12-12a.5.5 0 0 1 0-.708"/>
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-primary-custom w-100 text-white fw-semibold mb-2">
                Simpan & Perbarui Password
            </button>

            @if(!Auth::user()->must_change_password)
                <div class="text-center mt-3">
                    <a href="javascript:history.back()" class="text-decoration-none text-secondary fs-7">
                        &larr; Batal / Kembali
                    </a>
                </div>
            @endif
        </form>
    </div>

    <div class="bg-light p-3 text-center border-top text-muted fs-8">
        &copy; {{ date('Y') }} FEB - Universitas Kuningan
    </div>
</div>
@endsection
