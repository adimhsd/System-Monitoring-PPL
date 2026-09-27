<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'System Monitoring PPL FEB UNIKU')</title>

    <!-- Favicon Logo UNIKU -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo-uniku.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo-uniku.png') }}">

    <!-- Google Fonts Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }

        h1, h2, h3, h4, h5, h6, .font-heading {
            font-family: 'Outfit', sans-serif;
        }

        /* Sidebar Styling */
        .sidebar-desktop {
            width: 260px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1040;
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        }

        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .sidebar-menu {
            padding: 1rem 0.75rem;
            flex-grow: 1;
            overflow-y: auto;
        }

        .sidebar-label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            padding: 0.75rem 1rem 0.25rem;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem 1rem;
            color: #475569;
            font-weight: 600;
            font-size: 0.875rem;
            border-radius: 0.5rem;
            text-decoration: none;
            transition: all 0.2s ease;
            margin-bottom: 0.25rem;
        }

        .sidebar-link:hover {
            color: #0f172a;
            background-color: #f1f5f9;
        }

        .sidebar-link.active {
            color: #2563eb;
            background-color: #eff6ff;
            font-weight: 700;
        }

        /* Top Bar Styling */
        .topbar-custom {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.75rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 1030;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }

        /* Layout Wrappers */
        .main-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        @media (min-width: 992px) {
            .main-wrapper {
                margin-left: 260px;
            }
        }

        .badge-role-admin { background-color: #dc3545; color: #fff; }
        .badge-role-dpl { background-color: #0d6efd; color: #fff; }
        .badge-role-pic_mitra { background-color: #198754; color: #fff; }
        .badge-role-ketua_kelompok { background-color: #fd7e14; color: #fff; }
        
        .card-custom {
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03), 0 2px 4px -2px rgba(0,0,0,0.03);
            background: #ffffff;
        }
    </style>
</head>
<body>

    <!-- Desktop Left Sidebar -->
    <aside class="sidebar-desktop d-none d-lg-flex">
        <div class="sidebar-brand d-flex align-items-center gap-2 fw-bold fs-5 text-primary">
            <span class="bg-primary text-white rounded-3 px-2 py-1 fs-6">PPL</span>
            <span class="fs-6 text-dark">FEB UNIKU</span>
        </div>

        <div class="sidebar-menu">
            <div class="sidebar-label">Navigasi Utama</div>

            @if(Auth::user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span>📊</span> <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.dpl.index') }}" class="sidebar-link {{ request()->routeIs('admin.dpl.*') ? 'active' : '' }}">
                    <span>👨‍🏫</span> <span>Data DPL</span>
                </a>
                <a href="{{ route('admin.mitra.index') }}" class="sidebar-link {{ request()->routeIs('admin.mitra.*') ? 'active' : '' }}">
                    <span>🏢</span> <span>Data Mitra</span>
                </a>
                <a href="{{ route('admin.mahasiswa.index') }}" class="sidebar-link {{ request()->routeIs('admin.mahasiswa.*') ? 'active' : '' }}">
                    <span>👨‍🎓</span> <span>Data Mahasiswa</span>
                </a>
                <a href="{{ route('admin.kelompok.index') }}" class="sidebar-link {{ request()->routeIs('admin.kelompok.*') ? 'active' : '' }}">
                    <span>👥</span> <span>Data Kelompok</span>
                </a>
                <a href="{{ route('admin.plotting.index') }}" class="sidebar-link {{ request()->routeIs('admin.plotting.*') ? 'active' : '' }}">
                    <span>🗺️</span> <span>Plotting Kelompok</span>
                </a>
                <a href="{{ route('admin.monitoring.index') }}" class="sidebar-link {{ request()->routeIs('admin.monitoring.*') ? 'active' : '' }}">
                    <span>📍</span> <span>Monitoring DPL</span>
                </a>
                <a href="{{ route('admin.penilaian.index') }}" class="sidebar-link {{ request()->routeIs('admin.penilaian.*') ? 'active' : '' }}">
                    <span>📝</span> <span>Penilaian PPL</span>
                </a>
                <a href="{{ route('admin.luaran.index') }}" class="sidebar-link {{ request()->routeIs('admin.luaran.*') ? 'active' : '' }}">
                    <span>📂</span> <span>Luaran Akhir PPL</span>
                </a>
                <a href="{{ route('pedoman.index') }}" class="sidebar-link {{ request()->routeIs('pedoman.*') ? 'active' : '' }}">
                    <span>📖</span> <span>Buku Panduan/Pedoman</span>
                </a>
                <!-- Sub-Menu Dropdown Kelola User -->
                <div class="sidebar-dropdown">
                    <a class="sidebar-link d-flex justify-content-between align-items-center {{ request()->is('admin/users*') ? 'active' : '' }}" 
                       data-bs-toggle="collapse" 
                       href="#collapseKelolaUserDesktop" 
                       role="button" 
                       aria-expanded="{{ request()->is('admin/users*') ? 'true' : 'false' }}" 
                       aria-controls="collapseKelolaUserDesktop">
                        <div class="d-flex align-items-center gap-2">
                            <span>⚙️</span> <span>Kelola User</span>
                        </div>
                        <span class="fs-8 opacity-75">▼</span>
                    </a>
                    <div class="collapse {{ request()->is('admin/users*') ? 'show' : '' }} ps-3 pt-1" id="collapseKelolaUserDesktop">
                        <a href="{{ route('admin.users.index') }}" class="sidebar-link fs-8 py-1 mb-1 {{ request()->routeIs('admin.users.index') ? 'active text-primary fw-bold' : '' }}">
                            <span>📊</span> <span>Ringkasan Akun</span>
                        </a>
                        <a href="{{ route('admin.users.dpl') }}" class="sidebar-link fs-8 py-1 mb-1 {{ request()->routeIs('admin.users.dpl') ? 'active text-primary fw-bold' : '' }}">
                            <span>👨‍🏫</span> <span>Akun DPL</span>
                        </a>
                        <a href="{{ route('admin.users.pic') }}" class="sidebar-link fs-8 py-1 mb-1 {{ request()->routeIs('admin.users.pic') ? 'active text-primary fw-bold' : '' }}">
                            <span>🏢</span> <span>Akun PIC Mitra</span>
                        </a>
                        <a href="{{ route('admin.users.kelompok') }}" class="sidebar-link fs-8 py-1 mb-1 {{ request()->routeIs('admin.users.kelompok') ? 'active text-primary fw-bold' : '' }}">
                            <span>👥</span> <span>Akun Kelompok</span>
                        </a>
                    </div>
                </div>
            @elseif(Auth::user()->role === 'dpl')
                <a href="{{ route('dpl.dashboard') }}" class="sidebar-link {{ request()->routeIs('dpl.dashboard') ? 'active' : '' }}">
                    <span>📊</span> <span>Dashboard</span>
                </a>
                <a href="{{ route('dpl.monitoring.index') }}" class="sidebar-link {{ request()->routeIs('dpl.monitoring.*') ? 'active' : '' }}">
                    <span>📍</span> <span>Monitoring DPL</span>
                </a>
                <a href="{{ route('dpl.logbook.index') }}" class="sidebar-link {{ request()->routeIs('dpl.logbook.*') ? 'active' : '' }}">
                    <span>📘</span> <span>Logbook Harian</span>
                </a>
                <a href="{{ route('dpl.penilaian.index') }}" class="sidebar-link {{ request()->routeIs('dpl.penilaian.*') ? 'active' : '' }}">
                    <span>📝</span> <span>Penilaian PPL</span>
                </a>
                <a href="{{ route('dpl.luaran.index') }}" class="sidebar-link {{ request()->routeIs('dpl.luaran.*') ? 'active' : '' }}">
                    <span>📂</span> <span>Luaran Akhir PPL</span>
                </a>
                <a href="{{ route('pedoman.index') }}" class="sidebar-link {{ request()->routeIs('pedoman.*') ? 'active' : '' }}">
                    <span>📖</span> <span>Buku Panduan/Pedoman</span>
                </a>
            @elseif(Auth::user()->role === 'pic_mitra')
                <a href="{{ route('pic.dashboard') }}" class="sidebar-link {{ request()->routeIs('pic.dashboard') ? 'active' : '' }}">
                    <span>📊</span> <span>Dashboard</span>
                </a>
                <a href="{{ route('pic.logbook.index') }}" class="sidebar-link {{ request()->routeIs('pic.logbook.*') ? 'active' : '' }}">
                    <span>📘</span> <span>Logbook Harian</span>
                </a>
                <a href="{{ route('pic.penilaian.index') }}" class="sidebar-link {{ request()->routeIs('pic.penilaian.*') ? 'active' : '' }}">
                    <span>📝</span> <span>Penilaian Mitra</span>
                </a>
                <a href="{{ route('pedoman.index') }}" class="sidebar-link {{ request()->routeIs('pedoman.*') ? 'active' : '' }}">
                    <span>📖</span> <span>Buku Panduan/Pedoman</span>
                </a>
            @elseif(Auth::user()->role === 'ketua_kelompok')
                <a href="{{ route('student.dashboard') }}" class="sidebar-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                    <span>📊</span> <span>Dashboard</span>
                </a>
                <a href="{{ route('student.monitoring.index') }}" class="sidebar-link {{ request()->routeIs('student.monitoring.*') ? 'active' : '' }}">
                    <span>📍</span> <span>Kunjungan DPL</span>
                </a>
                <a href="{{ route('student.logbook.index') }}" class="sidebar-link {{ request()->routeIs('student.logbook.*') ? 'active' : '' }}">
                    <span>📘</span> <span>Logbook Harian</span>
                </a>
                <a href="{{ route('student.luaran.index') }}" class="sidebar-link {{ request()->routeIs('student.luaran.*') ? 'active' : '' }}">
                    <span>📂</span> <span>Luaran Akhir PPL</span>
                </a>
                <a href="{{ route('pedoman.index') }}" class="sidebar-link {{ request()->routeIs('pedoman.*') ? 'active' : '' }}">
                    <span>📖</span> <span>Buku Panduan/Pedoman</span>
                </a>
            @endif
        </div>
    </aside>

    <!-- Mobile Offcanvas Sidebar Drawer -->
    <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold text-primary d-flex align-items-center gap-2" id="offcanvasSidebarLabel">
                <span class="bg-primary text-white rounded-3 px-2 py-1 fs-6">PPL</span>
                <span class="text-dark">FEB UNIKU</span>
            </h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-3">
            <div class="sidebar-label">Navigasi Utama</div>

            @if(Auth::user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span>📊</span> <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.dpl.index') }}" class="sidebar-link {{ request()->routeIs('admin.dpl.*') ? 'active' : '' }}">
                    <span>👨‍🏫</span> <span>Data DPL</span>
                </a>
                <a href="{{ route('admin.mitra.index') }}" class="sidebar-link {{ request()->routeIs('admin.mitra.*') ? 'active' : '' }}">
                    <span>🏢</span> <span>Data Mitra</span>
                </a>
                <a href="{{ route('admin.mahasiswa.index') }}" class="sidebar-link {{ request()->routeIs('admin.mahasiswa.*') ? 'active' : '' }}">
                    <span>👨‍🎓</span> <span>Data Mahasiswa</span>
                </a>
                <a href="{{ route('admin.kelompok.index') }}" class="sidebar-link {{ request()->routeIs('admin.kelompok.*') ? 'active' : '' }}">
                    <span>👥</span> <span>Data Kelompok</span>
                </a>
                <a href="{{ route('admin.plotting.index') }}" class="sidebar-link {{ request()->routeIs('admin.plotting.*') ? 'active' : '' }}">
                    <span>🗺️</span> <span>Plotting Kelompok</span>
                </a>
                <a href="{{ route('admin.monitoring.index') }}" class="sidebar-link {{ request()->routeIs('admin.monitoring.*') ? 'active' : '' }}">
                    <span>📍</span> <span>Monitoring DPL</span>
                </a>
                <a href="{{ route('admin.penilaian.index') }}" class="sidebar-link {{ request()->routeIs('admin.penilaian.*') ? 'active' : '' }}">
                    <span>📝</span> <span>Penilaian PPL</span>
                </a>
                <a href="{{ route('admin.luaran.index') }}" class="sidebar-link {{ request()->routeIs('admin.luaran.*') ? 'active' : '' }}">
                    <span>📂</span> <span>Luaran Akhir PPL</span>
                </a>
                <a href="{{ route('pedoman.index') }}" class="sidebar-link {{ request()->routeIs('pedoman.*') ? 'active' : '' }}">
                    <span>📖</span> <span>Buku Panduan/Pedoman</span>
                </a>
                <!-- Sub-Menu Dropdown Kelola User Mobile -->
                <div class="sidebar-dropdown">
                    <a class="sidebar-link d-flex justify-content-between align-items-center {{ request()->is('admin/users*') ? 'active' : '' }}" 
                       data-bs-toggle="collapse" 
                       href="#collapseKelolaUserMobile" 
                       role="button" 
                       aria-expanded="{{ request()->is('admin/users*') ? 'true' : 'false' }}" 
                       aria-controls="collapseKelolaUserMobile">
                        <div class="d-flex align-items-center gap-2">
                            <span>⚙️</span> <span>Kelola User</span>
                        </div>
                        <span class="fs-8 opacity-75">▼</span>
                    </a>
                    <div class="collapse {{ request()->is('admin/users*') ? 'show' : '' }} ps-3 pt-1" id="collapseKelolaUserMobile">
                        <a href="{{ route('admin.users.index') }}" class="sidebar-link fs-8 py-1 mb-1 {{ request()->routeIs('admin.users.index') ? 'active text-primary fw-bold' : '' }}">
                            <span>📊</span> <span>Ringkasan Akun</span>
                        </a>
                        <a href="{{ route('admin.users.dpl') }}" class="sidebar-link fs-8 py-1 mb-1 {{ request()->routeIs('admin.users.dpl') ? 'active text-primary fw-bold' : '' }}">
                            <span>👨‍🏫</span> <span>Akun DPL</span>
                        </a>
                        <a href="{{ route('admin.users.pic') }}" class="sidebar-link fs-8 py-1 mb-1 {{ request()->routeIs('admin.users.pic') ? 'active text-primary fw-bold' : '' }}">
                            <span>🏢</span> <span>Akun PIC Mitra</span>
                        </a>
                        <a href="{{ route('admin.users.kelompok') }}" class="sidebar-link fs-8 py-1 mb-1 {{ request()->routeIs('admin.users.kelompok') ? 'active text-primary fw-bold' : '' }}">
                            <span>👥</span> <span>Akun Kelompok</span>
                        </a>
                    </div>
                </div>
            @elseif(Auth::user()->role === 'dpl')
                <a href="{{ route('dpl.dashboard') }}" class="sidebar-link {{ request()->routeIs('dpl.dashboard') ? 'active' : '' }}">
                    <span>📊</span> <span>Dashboard</span>
                </a>
                <a href="{{ route('dpl.monitoring.index') }}" class="sidebar-link {{ request()->routeIs('dpl.monitoring.*') ? 'active' : '' }}">
                    <span>📍</span> <span>Monitoring DPL</span>
                </a>
                <a href="{{ route('dpl.logbook.index') }}" class="sidebar-link {{ request()->routeIs('dpl.logbook.*') ? 'active' : '' }}">
                    <span>📘</span> <span>Logbook Harian</span>
                </a>
                <a href="{{ route('dpl.penilaian.index') }}" class="sidebar-link {{ request()->routeIs('dpl.penilaian.*') ? 'active' : '' }}">
                    <span>📝</span> <span>Penilaian PPL</span>
                </a>
                <a href="{{ route('dpl.luaran.index') }}" class="sidebar-link {{ request()->routeIs('dpl.luaran.*') ? 'active' : '' }}">
                    <span>📂</span> <span>Luaran Akhir PPL</span>
                </a>
                <a href="{{ route('pedoman.index') }}" class="sidebar-link {{ request()->routeIs('pedoman.*') ? 'active' : '' }}">
                    <span>📖</span> <span>Buku Panduan/Pedoman</span>
                </a>
            @elseif(Auth::user()->role === 'pic_mitra')
                <a href="{{ route('pic.dashboard') }}" class="sidebar-link {{ request()->routeIs('pic.dashboard') ? 'active' : '' }}">
                    <span>📊</span> <span>Dashboard</span>
                </a>
                <a href="{{ route('pic.logbook.index') }}" class="sidebar-link {{ request()->routeIs('pic.logbook.*') ? 'active' : '' }}">
                    <span>📘</span> <span>Logbook Harian</span>
                </a>
                <a href="{{ route('pic.penilaian.index') }}" class="sidebar-link {{ request()->routeIs('pic.penilaian.*') ? 'active' : '' }}">
                    <span>📝</span> <span>Penilaian Mitra</span>
                </a>
                <a href="{{ route('pedoman.index') }}" class="sidebar-link {{ request()->routeIs('pedoman.*') ? 'active' : '' }}">
                    <span>📖</span> <span>Buku Panduan/Pedoman</span>
                </a>
            @elseif(Auth::user()->role === 'ketua_kelompok')
                <a href="{{ route('student.dashboard') }}" class="sidebar-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                    <span>📊</span> <span>Dashboard</span>
                </a>
                <a href="{{ route('student.monitoring.index') }}" class="sidebar-link {{ request()->routeIs('student.monitoring.*') ? 'active' : '' }}">
                    <span>📍</span> <span>Kunjungan DPL</span>
                </a>
                <a href="{{ route('student.logbook.index') }}" class="sidebar-link {{ request()->routeIs('student.logbook.*') ? 'active' : '' }}">
                    <span>📘</span> <span>Logbook Harian</span>
                </a>
                <a href="{{ route('student.luaran.index') }}" class="sidebar-link {{ request()->routeIs('student.luaran.*') ? 'active' : '' }}">
                    <span>📂</span> <span>Luaran Akhir PPL</span>
                </a>
                <a href="{{ route('pedoman.index') }}" class="sidebar-link {{ request()->routeIs('pedoman.*') ? 'active' : '' }}">
                    <span>📖</span> <span>Buku Panduan/Pedoman</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Main Content Layout Wrapper -->
    <div class="main-wrapper">
        <!-- Topbar Header -->
        <header class="topbar-custom d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-outline-secondary btn-sm d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar" aria-controls="offcanvasSidebar">
                    ☰ Menu
                </button>
                <span class="fw-semibold text-secondary fs-7 d-none d-md-inline">
                    Sistem Pemantauan & Penilaian PPL FEB UNIKU
                </span>
            </div>

            <div class="d-flex align-items-center gap-3">
                @if(Auth::check() && Auth::user()->role === 'admin')
                    <!-- Backup DB Button for Admin -->
                    <a href="{{ route('admin.backup.download') }}" class="btn btn-outline-success btn-sm fw-semibold d-flex align-items-center gap-1 rounded-pill px-3 shadow-sm" title="Backup Database SQL">
                        <span>💾</span> <span>Backup DB</span>
                    </a>
                @endif

                <!-- Dropdown Notifikasi Real-Time -->
                <div class="dropdown" id="notificationDropdownContainer">
                    <button class="btn btn-light position-relative p-2 rounded-circle border" type="button" id="dropdownNotification" data-bs-toggle="dropdown" aria-expanded="false">
                        🔔
                        <span id="notificationBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none">
                            0
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end shadow border-0 p-0 fs-7" style="width: 320px;" aria-labelledby="dropdownNotification">
                        <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0">Notifikasi</h6>
                            <button id="markAllReadBtn" class="btn btn-link btn-sm text-decoration-none p-0 fs-8">Tandai Semua Dibaca</button>
                        </div>
                        <div id="notificationList" class="overflow-auto" style="max-height: 300px;">
                            <div class="p-3 text-center text-muted fs-8">Tidak ada notifikasi baru.</div>
                        </div>
                    </div>
                </div>

                <!-- Profile & Logout Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2 border px-3 py-1 rounded-pill" type="button" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="fw-bold fs-7">{{ Auth::user()->nama_lengkap ?? Auth::user()->username }}</span>
                        <span class="badge badge-role-{{ Auth::user()->role }} text-capitalize px-2 py-1 fs-8">
                            {{ str_replace('_', ' ', Auth::user()->role) }}
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 fs-7" aria-labelledby="dropdownUser">
                        <li>
                            <a class="dropdown-item" href="{{ route('password.change.form') }}">
                                🔑 Ubah Password
                            </a>
                        </li>
                        @if(Auth::user()->role === 'admin')
                            <li>
                                <a class="dropdown-item text-success fw-semibold" href="{{ route('admin.backup.download') }}">
                                    💾 Backup Database (.sql)
                                </a>
                            </li>
                        @endif
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger fw-semibold">
                                    🚪 Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Main Body Content -->
        <main class="flex-grow-1 p-3 p-md-4">
            <!-- Flash Message Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show card-custom border-success mb-4" role="alert">
                    <strong>Sukses!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show card-custom border-danger mb-4" role="alert">
                    <strong>Error!</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Real-Time In-App Notification Polling Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const badge = document.getElementById('notificationBadge');
            const list = document.getElementById('notificationList');
            const markAllBtn = document.getElementById('markAllReadBtn');

            function fetchNotifications() {
                fetch('{{ route("notifications.index") }}')
                    .then(res => res.json())
                    .then(data => {
                        if (data.unread_count > 0) {
                            badge.textContent = data.unread_count;
                            badge.classList.remove('d-none');
                        } else {
                            badge.classList.add('d-none');
                        }

                        if (data.notifications.length === 0) {
                            list.innerHTML = '<div class="p-3 text-center text-muted fs-8">Tidak ada notifikasi.</div>';
                            return;
                        }

                        list.innerHTML = data.notifications.map(n => `
                            <div class="p-3 border-bottom ${n.is_read ? 'bg-white' : 'bg-primary bg-opacity-10'} notification-item" data-id="${n.id}">
                                <div class="fw-bold text-dark fs-8">${n.judul}</div>
                                <div class="text-secondary fs-8">${n.pesan}</div>
                                <div class="text-muted fs-8 mt-1">${new Date(n.created_at).toLocaleString('id-ID')}</div>
                            </div>
                        `).join('');
                    })
                    .catch(err => console.error('Notification error:', err));
            }

            if (markAllBtn) {
                markAllBtn.addEventListener('click', function() {
                    fetch('{{ route("notifications.readAll") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    }).then(() => fetchNotifications());
                });
            }

            fetchNotifications();
            setInterval(fetchNotifications, 30000);
        });
    </script>
</body>
</html>
