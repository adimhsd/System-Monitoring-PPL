<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Pendaftaran') — PPL FEB UNIKU</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo-uniku.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #f0f4f9 0%, #e2e8f0 100%);
            min-height: 100vh;
        }
        .form-card {
            background: #ffffff;
            border-radius: 1.25rem;
            box-shadow: 0 15px 35px rgba(15, 23, 42, 0.08), 0 5px 15px rgba(15, 23, 42, 0.04);
            border: 1px solid rgba(226, 232, 240, 0.8);
            overflow: hidden;
            width: 100%;
            max-width: 760px;
        }
        .form-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: #ffffff;
            padding: 2rem 2rem 1.5rem;
            text-align: center;
        }
        .form-header .brand-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.18);
            padding: 0.35rem 0.85rem;
            border-radius: 50rem;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 0.75rem;
        }
        .form-section-title {
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #0d6efd;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 0.4rem;
            margin: 1.5rem 0 1rem;
        }
        .form-control, .form-select {
            border-radius: 0.65rem;
            padding: 0.65rem 0.9rem;
            border-color: #cbd5e1;
        }
        .form-control:focus, .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }
        .fs-7 { font-size: 0.875rem; }
        .fs-8 { font-size: 0.78rem; }
        .btn-primary-custom {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            border: none;
            border-radius: 0.65rem;
            padding: 0.85rem 1.25rem;
            font-weight: 600;
            min-height: 48px;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25);
        }
        .pilihan-card { cursor: pointer; transition: all .15s ease-in-out; }
        .pilihan-card:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(13,110,253,.12); }
        .btn-check:checked + .pilihan-card { border-color: #0d6efd !important; background: #eff6ff; }
    </style>
</head>
<body>
    <main class="container py-4 px-3 d-flex justify-content-center">
        <div class="form-card">
            <div class="form-header">
                <div class="mb-2">
                    <img src="{{ asset('images/logo-uniku.png') }}" alt="Logo Universitas Kuningan" style="height: 64px; width: auto;">
                </div>
                <span class="brand-badge">PPL FEB UNIKU</span>
                <h4 class="fw-bold mb-1">@yield('heading')</h4>
                <p class="mb-0 text-white-50 fs-7">@yield('subheading')</p>
            </div>
            <div class="p-4 p-md-5 pt-md-4">
                @if (session('error'))
                    <div class="alert alert-danger fs-7">{{ session('error') }}</div>
                @endif
                @yield('content')
            </div>
            <div class="bg-light p-3 text-center border-top text-muted fs-8">
                &copy; {{ date('Y') }} Fakultas Ekonomi dan Bisnis — Universitas Kuningan
            </div>
        </div>
    </main>
    @stack('scripts')
</body>
</html>
