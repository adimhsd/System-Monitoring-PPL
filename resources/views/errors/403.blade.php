<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Akses Dibatasi | FEB UNIKU</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-uniku.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #f0f4f9 0%, #e2e8f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .error-card {
            background: #ffffff;
            border-radius: 1.25rem;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08);
            border: 1px solid rgba(226, 232, 240, 0.8);
            max-width: 480px;
            width: 100%;
            text-align: center;
            overflow: hidden;
        }
        .error-header {
            background: linear-gradient(135deg, #dc3545 0%, #b02a37 100%);
            color: #ffffff;
            padding: 2.5rem 2rem 2rem;
        }
        .error-code {
            font-size: 4rem;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -1px;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 10px rgba(0,0,0,0.15);
        }
        .btn-home {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: #ffffff;
            border: none;
            border-radius: 0.65rem;
            padding: 0.85rem 1.75rem;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25);
            transition: all 0.2s ease-in-out;
            text-decoration: none;
            display: inline-block;
        }
        .btn-home:hover {
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(13, 110, 253, 0.35);
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-header">
            <img src="{{ asset('images/logo-uniku.png') }}" alt="Logo UNIKU" style="height: 70px; width: auto; filter: drop-shadow(0 2px 6px rgba(0,0,0,0.2));" class="mb-3">
            <div class="error-code">403</div>
            <h5 class="fw-bold mb-0">Akses Dibatasi</h5>
        </div>
        <div class="p-4">
            <p class="text-secondary mb-4">
                {{ $exception->getMessage() ?: 'Anda tidak memiliki hak akses untuk membuka halaman ini.' }}
            </p>
            @auth
                @php
                    $dashboardRoute = match(auth()->user()->role) {
                        'admin' => route('admin.dashboard'),
                        'dpl' => route('dpl.dashboard'),
                        'pic_mitra' => route('pic.dashboard'),
                        'ketua_kelompok' => route('ketua.dashboard'),
                        default => route('login'),
                    };
                @endphp
                <a href="{{ $dashboardRoute }}" class="btn-home">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-house-door-fill me-2" viewBox="0 0 16 16">
                        <path d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.505a.5.5 0 0 0 .5.5h2a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h2a.5.5 0 0 0 .5-.5"/>
                    </svg>
                    Kembali ke Dashboard Saya
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-home">
                    Kembali ke Halaman Login
                </a>
            @endauth
        </div>
        <div class="bg-light p-3 border-top text-center text-muted fs-7">
            &copy; 2026 FEB - Universitas Kuningan
        </div>
    </div>
</body>
</html>
