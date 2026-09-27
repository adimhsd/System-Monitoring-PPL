<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login') — Sistem Monitoring PPL FEB UNIKU</title>

    <!-- Favicon Logo UNIKU -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo-uniku.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo-uniku.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Vite Assets (Bootstrap 5.3 + Alpine.js) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #f0f4f9 0%, #e2e8f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-card {
            background: #ffffff;
            border-radius: 1.25rem;
            box-shadow: 0 15px 35px rgba(15, 23, 42, 0.08), 0 5px 15px rgba(15, 23, 42, 0.04);
            border: 1px solid rgba(226, 232, 240, 0.8);
            overflow: hidden;
            width: 100%;
            max-width: 440px;
        }
        .auth-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: #ffffff;
            padding: 2.25rem 2rem 1.75rem;
            text-align: center;
        }
        .auth-header .brand-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(8px);
            padding: 0.35rem 0.85rem;
            border-radius: 50rem;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 0.75rem;
        }
        .form-control {
            border-radius: 0.65rem;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            border-color: #cbd5e1;
        }
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }
        .btn-primary-custom {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            border: none;
            border-radius: 0.65rem;
            padding: 0.85rem 1.25rem;
            font-weight: 600;
            min-height: 48px;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25);
            transition: all 0.2s ease-in-out;
        }
        .btn-primary-custom:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(13, 110, 253, 0.35);
        }
    </style>
</head>
<body>

    <main class="container p-3">
        @yield('content')
    </main>

</body>
</html>
