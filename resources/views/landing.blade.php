<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Monitoring & Penilaian PPL — Fakultas Ekonomi dan Bisnis Universitas Kuningan</title>

    <!-- Favicon Logo UNIKU -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo-uniku.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo-uniku.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Vite Assets (Bootstrap 5.3 + Alpine.js) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            --hero-bg: linear-gradient(135deg, #0a192f 0%, #0f2b5c 50%, #1a365d 100%);
            --card-hover: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1e293b;
            background-color: #f8fafc;
            overflow-x: hidden;
        }

        /* Glassmorphism Navbar */
        .landing-nav {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            transition: var(--card-hover);
        }

        /* Hero Section Styling */
        .hero-section {
            background: var(--hero-bg);
            color: #ffffff;
            position: relative;
            padding: 5.5rem 0 6rem;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: -20%;
            right: -10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(13, 110, 253, 0.25) 0%, rgba(13, 110, 253, 0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .hero-section::after {
            content: '';
            position: absolute;
            bottom: -15%;
            left: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.2) 0%, rgba(14, 165, 233, 0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .hero-badge {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(8px);
            color: #e0f2fe;
            font-size: 0.82rem;
            font-weight: 600;
            padding: 0.45rem 1.15rem;
            border-radius: 50rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Stat Card */
        .stat-card {
            background: #ffffff;
            border-radius: 1.25rem;
            border: 1px solid #e2e8f0;
            padding: 1.5rem 1.25rem;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.05);
            transition: var(--card-hover);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 30px -10px rgba(13, 110, 253, 0.15);
            border-color: #bfdbfe;
        }

        /* Feature Card */
        .feature-card {
            background: #ffffff;
            border-radius: 1.25rem;
            border: 1px solid #e2e8f0;
            padding: 2rem 1.75rem;
            height: 100%;
            transition: var(--card-hover);
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 35px -10px rgba(15, 23, 42, 0.08);
            border-color: #cbd5e1;
        }

        .feature-icon-wrapper {
            width: 58px;
            height: 58px;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            margin-bottom: 1.25rem;
        }

        /* Workflow Step */
        .workflow-step {
            position: relative;
            background: #ffffff;
            border-radius: 1.25rem;
            border: 1px solid #e2e8f0;
            padding: 1.75rem 1.5rem;
            transition: var(--card-hover);
            height: 100%;
        }

        .workflow-step:hover {
            border-color: #93c5fd;
            box-shadow: 0 15px 30px -8px rgba(13, 110, 253, 0.12);
            transform: translateY(-3px);
        }

        .step-number {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #eff6ff;
            color: #0d6efd;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            border: 2px solid #bfdbfe;
            margin-bottom: 1rem;
        }

        /* Role Card */
        .role-card {
            border-radius: 1.25rem;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            padding: 1.75rem;
            height: 100%;
            transition: var(--card-hover);
        }

        .role-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 30px rgba(15, 23, 42, 0.06);
        }

        /* CTA Buttons */
        .btn-cta-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: #ffffff !important;
            border: none;
            padding: 0.85rem 1.75rem;
            font-weight: 600;
            border-radius: 0.75rem;
            box-shadow: 0 8px 20px rgba(13, 110, 253, 0.35);
            transition: var(--card-hover);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-cta-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(13, 110, 253, 0.45);
        }

        .btn-cta-light {
            background: #ffffff;
            color: #0f172a !important;
            border: 1px solid #e2e8f0;
            padding: 0.85rem 1.5rem;
            font-weight: 600;
            border-radius: 0.75rem;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
            transition: var(--card-hover);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-cta-light:hover {
            background: #f1f5f9;
            transform: translateY(-2px);
        }

        .btn-cta-outline-light {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 0.85rem 1.5rem;
            font-weight: 600;
            border-radius: 0.75rem;
            backdrop-filter: blur(8px);
            transition: var(--card-hover);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-cta-outline-light:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: #ffffff;
            transform: translateY(-2px);
        }

        /* Banner CTA */
        .cta-banner {
            background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%);
            border-radius: 1.75rem;
            color: #ffffff;
            padding: 3.5rem 2.5rem;
            position: relative;
            overflow: hidden;
        }

        .footer-landing {
            background: #0f172a;
            color: #94a3b8;
            padding: 4.5rem 0 2rem;
            font-size: 0.9rem;
        }

        .footer-landing a {
            color: #cbd5e1;
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer-landing a:hover {
            color: #60a5fa;
        }
    </style>
</head>
<body>

    <!-- ================= NAVBAR ================= -->
    <nav class="navbar navbar-expand-lg landing-nav sticky-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
                <img src="{{ asset('images/logo-uniku.png') }}" alt="Logo UNIKU" style="height: 46px; width: auto;">
                <div class="d-flex flex-column">
                    <span class="fw-bold fs-6 text-dark lh-sm">PPL FEB UNIKU</span>
                    <span class="text-muted" style="font-size: 0.72rem; letter-spacing: 0.5px;">UNIVERSITAS KUNINGAN</span>
                </div>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-3">
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-secondary" href="#beranda">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-secondary" href="#tentang">Tentang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-secondary" href="#workflow">Alur Kerja</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-secondary" href="#peran">Kendali Peran</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-secondary" href="#statistik">Statistik</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-secondary" href="{{ route('pedoman.index') }}">Buku Panduan</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <div class="dropdown">
                        <button class="btn btn-outline-primary fw-semibold dropdown-toggle px-3" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-pencil-square me-1"></i> Pendaftaran
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-2">
                            <li><h6 class="dropdown-header text-uppercase fs-8 text-muted">Formulir Online</h6></li>
                            <li>
                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('pendaftaran.mahasiswa') }}">
                                    <i class="bi bi-mortarboard text-primary"></i> Mahasiswa PPL
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('pendaftaran.dpl') }}">
                                    <i class="bi bi-person-badge text-success"></i> Calon DPL
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('pendaftaran.index') }}">
                                    <i class="bi bi-info-circle text-info"></i> Info Portal Pendaftaran
                                </a>
                            </li>
                        </ul>
                    </div>

                    <a href="{{ route('login') }}" class="btn btn-cta-primary px-3 py-2">
                        <i class="bi bi-box-arrow-in-right"></i> Masuk Sistem
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- ================= HERO SECTION ================= -->
    <header id="beranda" class="hero-section">
        <div class="container position-relative" style="z-index: 2;">
            <div class="row align-items-center gy-5">
                <div class="col-lg-7">
                    <div class="hero-badge mb-3">
                        <i class="bi bi-patch-check-fill text-warning"></i>
                        <span>Sistem Resmi Fakultas Ekonomi & Bisnis UNIKU</span>
                    </div>
                    <h1 class="display-5 fw-bold text-white mb-3 lh-sm">
                        Sistem Pemantauan & Penilaian PPL Terintegrasi
                    </h1>
                    <p class="lead text-white-50 mb-4 fs-6">
                        Platform digital untuk pelaporan aktivitas jurnal harian (logbook), supervisi kunjungan DPL, serta evaluasi penilaian objektif <strong>(60% Pembimbing Mitra + 40% Dosen Pembimbing Lapangan)</strong> bagi mahasiswa Manajemen, Akuntansi, dan Bisnis Digital.
                    </p>

                    <div class="d-flex flex-wrap gap-3 mb-4">
                        <a href="{{ route('login') }}" class="btn btn-cta-primary">
                            <i class="bi bi-box-arrow-in-right fs-5"></i>
                            <span>Masuk ke Sistem (Login)</span>
                        </a>
                        <a href="{{ route('pendaftaran.mahasiswa') }}" class="btn btn-cta-outline-light">
                            <i class="bi bi-person-plus fs-5"></i>
                            <span>Daftar Mahasiswa PPL</span>
                        </a>
                        <a href="{{ route('pendaftaran.dpl') }}" class="btn btn-cta-outline-light">
                            <i class="bi bi-person-badge fs-5"></i>
                            <span>Daftar Calon DPL</span>
                        </a>
                    </div>

                    <div class="d-flex align-items-center gap-3 pt-2 text-white-50 fs-7">
                        <div class="d-flex align-items-center gap-1">
                            <i class="bi bi-check-circle-fill text-success"></i> Reguler & MBKM
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <i class="bi bi-check-circle-fill text-success"></i> Real-time Logbook
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <i class="bi bi-check-circle-fill text-success"></i> Nilai Mutu Otomatis (A–E)
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 text-center">
                    <div class="position-relative d-inline-block">
                        <div class="p-4 rounded-4" style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15); backdrop-filter: blur(12px); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">
                            <img src="{{ asset('images/logo-uniku.png') }}" alt="Logo FEB UNIKU" class="img-fluid mb-3" style="max-height: 140px; filter: drop-shadow(0 8px 16px rgba(0,0,0,0.3));">
                            <h5 class="fw-bold text-white mb-1">FAKULTAS EKONOMI & BISNIS</h5>
                            <p class="text-white-50 fs-7 mb-3">Universitas Kuningan — Cerdas, Mandiri, Berakhlak Mulia</p>
                            <div class="d-flex justify-content-center gap-2">
                                <span class="badge bg-primary bg-opacity-75 px-3 py-2 rounded-pill">S1 Manajemen</span>
                                <span class="badge bg-success bg-opacity-75 px-3 py-2 rounded-pill">S1 Akuntansi</span>
                                <span class="badge bg-info bg-opacity-75 px-3 py-2 rounded-pill">S1 Bisnis Digital</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- ================= STATISTIK SECTION ================= -->
    <section id="statistik" class="py-5" style="margin-top: -3rem; position: relative; z-index: 3;">
        <div class="container">
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <div class="stat-card text-center">
                        <div class="text-primary mb-1 fs-2"><i class="bi bi-people-fill"></i></div>
                        <h2 class="fw-bold text-dark mb-0">{{ number_format($stats['total_mahasiswa'] ?? 0) }}</h2>
                        <span class="text-muted fs-7 fw-medium">Mahasiswa Terdaftar</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card text-center">
                        <div class="text-success mb-1 fs-2"><i class="bi bi-grid-fill"></i></div>
                        <h2 class="fw-bold text-dark mb-0">{{ number_format($stats['total_kelompok'] ?? 0) }}</h2>
                        <span class="text-muted fs-7 fw-medium">Kelompok PPL (Reguler & MBKM)</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card text-center">
                        <div class="text-warning mb-1 fs-2"><i class="bi bi-building"></i></div>
                        <h2 class="fw-bold text-dark mb-0">{{ number_format($stats['total_mitra'] ?? 0) }}</h2>
                        <span class="text-muted fs-7 fw-medium">Instansi Mitra Magang</span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card text-center">
                        <div class="text-info mb-1 fs-2"><i class="bi bi-person-video3"></i></div>
                        <h2 class="fw-bold text-dark mb-0">{{ number_format($stats['total_dpl'] ?? 0) }}</h2>
                        <span class="text-muted fs-7 fw-medium">Dosen Pembimbing (DPL)</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= TENTANG & TUJUAN ================= -->
    <section id="tentang" class="py-5">
        <div class="container">
            <div class="text-center max-w-xl mx-auto mb-5">
                <span class="badge bg-primary-subtle text-primary fw-bold text-uppercase px-3 py-2 rounded-pill fs-8">Tentang PPL FEB</span>
                <h2 class="fw-bold mt-2">Tujuan & Fondasi Program Praktik Pengenalan Lapangan</h2>
                <p class="text-secondary">PPL merupakan mata kuliah wajib penerapan keilmuan ekonomi dan bisnis pada lingkungan kerja instansi pemerintah (SKPD), BUMN, perusahaan swasta, dan UMKM.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper bg-primary-subtle text-primary">
                            <i class="bi bi-briefcase-fill"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Aplikasi Kompetensi Nyata</h5>
                        <p class="text-secondary fs-7 mb-0">
                            Memberikan mahasiswa ruang adaptasi kerja profesional untuk mempraktikkan teori manajemen pemasaran, keuangan, akuntansi, dan teknologi bisnis digital di lapangan.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper bg-success-subtle text-success">
                            <i class="bi bi-award-fill"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Penilaian Objektif 60:40</h5>
                        <p class="text-secondary fs-7 mb-0">
                            Evaluasi kinerja terukur secara adil: <strong>60% nilai lapangan</strong> diisi oleh PIC Mitra (kedisiplinan, kerjasama, etika) dan <strong>40% nilai akademik</strong> oleh DPL (laporan & presentasi).
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper bg-info-subtle text-info">
                            <i class="bi bi-diagram-3-fill"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Rekognisi Program MBKM</h5>
                        <p class="text-secondary fs-7 mb-0">
                            Mendukung penyelarasan kurikulum Merdeka Belajar Kampus Merdeka (MBKM) dengan tata kelola khusus bagi mahasiswa peserta magang mandiri bersertifikat.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= WORKFLOW & ALUR KERJA ================= -->
    <section id="workflow" class="py-5 bg-white border-top border-bottom">
        <div class="container">
            <div class="text-center max-w-xl mx-auto mb-5">
                <span class="badge bg-success-subtle text-success fw-bold text-uppercase px-3 py-2 rounded-pill fs-8">Alur Kerja Sistem</span>
                <h2 class="fw-bold mt-2">Workflow Pelaksanaan & Pemantauan PPL</h2>
                <p class="text-secondary">Sistem mengintegrasikan seluruh tahapan kegiatan dari pendaftaran hingga rekapitulasi nilai akhir secara terstruktur.</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="workflow-step">
                        <div class="step-number">1</div>
                        <h6 class="fw-bold mb-2">Pendaftaran Mandiri Online</h6>
                        <p class="text-secondary fs-7 mb-0">
                            Calon peserta PPL (Reguler/MBKM) dan Dosen mendaftar secara online dengan melampirkan berkas persyaratan tanpa perlu proses manual di kampus.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="workflow-step">
                        <div class="step-number">2</div>
                        <h6 class="fw-bold mb-2">Plotting Kelompok & Mitra</h6>
                        <p class="text-secondary fs-7 mb-0">
                            Admin Fakultas memplot mahasiswa ke instansi mitra magang dan menetapkan Dosen Pembimbing Lapangan dengan kontrol kuota bimbingan terdistribusi.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="workflow-step">
                        <div class="step-number">3</div>
                        <h6 class="fw-bold mb-2">Jurnal Harian (Logbook)</h6>
                        <p class="text-secondary fs-7 mb-0">
                            Ketua kelompok mendokumentasikan kegiatan harian beserta foto aktivitas kerja, kemudian ditinjau oleh PIC Mitra dan DPL melalui fitur konfirmasi "Sudah Dilihat".
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="workflow-step">
                        <div class="step-number">4</div>
                        <h6 class="fw-bold mb-2">Supervisi Kunjungan DPL</h6>
                        <p class="text-secondary fs-7 mb-0">
                            DPL melaksanakan supervisi lapangan langsung ke instansi mitra, mendokumentasikan catatan arahan, serta divalidasi oleh mahasiswa kelompok.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="workflow-step">
                        <div class="step-number">5</div>
                        <h6 class="fw-bold mb-2">Pengunggahan 3 Luaran Akhir</h6>
                        <p class="text-secondary fs-7 mb-0">
                            Ketua kelompok menyerahkan 3 jenis luaran akhir: Laporan Akhir Kegiatan (PDF maks. 5MB), Link URL Video Dokumentasi (YouTube), dan Poster Kegiatan resolusi tinggi (Format Gambar maks. 3MB).
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="workflow-step">
                        <div class="step-number">6</div>
                        <h6 class="fw-bold mb-2">Penilaian Terpadu & Huruf Mutu</h6>
                        <p class="text-secondary fs-7 mb-0">
                            Penggabungan otomatis 60% Nilai Mitra + 40% Nilai Laporan DPL membentuk Nilai Akhir angka dan grade Huruf Mutu (A, AB, B, BC, C, CD, D, E) secara final dan terkunci.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= KENDALI DATA & HAK AKSES PERAN ================= -->
    <section id="peran" class="py-5">
        <div class="container">
            <div class="text-center max-w-xl mx-auto mb-5">
                <span class="badge bg-warning-subtle text-warning fw-bold text-uppercase px-3 py-2 rounded-pill fs-8">Matriks Akses</span>
                <h2 class="fw-bold mt-2">Kendali Data Berdasarkan Peran Pengguna</h2>
                <p class="text-secondary">Aplikasi menerapkan Role-Based Access Control (RBAC) untuk menjamin keamanan, kerahasiaan, dan akuntabilitas data nilai.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="role-card">
                        <div class="fs-2 mb-2 text-primary"><i class="bi bi-mortarboard-fill"></i></div>
                        <h5 class="fw-bold mb-1">Mahasiswa / Ketua</h5>
                        <p class="text-muted fs-8 mb-3">Akun Kelompok Mandiri</p>
                        <ul class="list-unstyled text-secondary fs-7 mb-0 d-flex flex-column gap-2">
                            <li><i class="bi bi-check2 text-primary me-1"></i> Input logbook & foto harian</li>
                            <li><i class="bi bi-check2 text-primary me-1"></i> Upload Laporan Akhir PDF</li>
                            <li><i class="bi bi-check2 text-primary me-1"></i> Input link video presentasi</li>
                            <li><i class="bi bi-check2 text-primary me-1"></i> Unduh PDF Jurnal Logbook</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="role-card">
                        <div class="fs-2 mb-2 text-success"><i class="bi bi-person-video3"></i></div>
                        <h5 class="fw-bold mb-1">Dosen DPL</h5>
                        <p class="text-muted fs-8 mb-3">Pembimbing Lapangan</p>
                        <ul class="list-unstyled text-secondary fs-7 mb-0 d-flex flex-column gap-2">
                            <li><i class="bi bi-check2 text-success me-1"></i> Verifikasi logbook bimbingan</li>
                            <li><i class="bi bi-check2 text-success me-1"></i> Input agenda kunjungan monitoring</li>
                            <li><i class="bi bi-check2 text-success me-1"></i> Input Nilai Laporan DPL (40%)</li>
                            <li><i class="bi bi-check2 text-success me-1"></i> Kunci nilai & ekspor Excel DPL</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="role-card">
                        <div class="fs-2 mb-2 text-warning"><i class="bi bi-building-check"></i></div>
                        <h5 class="fw-bold mb-1">PIC Mitra</h5>
                        <p class="text-muted fs-8 mb-3">Instansi / DUDI</p>
                        <ul class="list-unstyled text-secondary fs-7 mb-0 d-flex flex-column gap-2">
                            <li><i class="bi bi-check2 text-warning me-1"></i> Pemantauan kehadiran lapangan</li>
                            <li><i class="bi bi-check2 text-warning me-1"></i> Paraf logbook harian instansi</li>
                            <li><i class="bi bi-check2 text-warning me-1"></i> Input Nilai Mitra Lapangan (60%)</li>
                            <li><i class="bi bi-check2 text-warning me-1"></i> Catatan evaluasi kerja mahasiswa</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="role-card">
                        <div class="fs-2 mb-2 text-danger"><i class="bi bi-shield-lock-fill"></i></div>
                        <h5 class="fw-bold mb-1">Administrator</h5>
                        <p class="text-muted fs-8 mb-3">Unit PPL Fakultas</p>
                        <ul class="list-unstyled text-secondary fs-7 mb-0 d-flex flex-column gap-2">
                            <li><i class="bi bi-check2 text-danger me-1"></i> Master data mahasiswa, DPL, mitra</li>
                            <li><i class="bi bi-check2 text-danger me-1"></i> Plotting & manajemen akun</li>
                            <li><i class="bi bi-check2 text-danger me-1"></i> Buka/kunci nilai & konfigurasi skala</li>
                            <li><i class="bi bi-check2 text-danger me-1"></i> Ekspor rekap nilai & backup SQL</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= CTA BANNER ================= -->
    <section class="py-5">
        <div class="container">
            <div class="cta-banner">
                <div class="row align-items-center gy-4">
                    <div class="col-lg-8">
                        <span class="badge bg-light text-dark fw-bold text-uppercase px-3 py-2 rounded-pill fs-8 mb-3">Layanan Terpadu</span>
                        <h3 class="display-6 fw-bold text-white mb-2">Siap Melaksanakan Praktik Pengenalan Lapangan?</h3>
                        <p class="text-white-50 mb-0">
                            Akses sistem sekarang untuk mengisi jurnal kegiatan harian, mengajukan pendaftaran bimbingan, atau melakukan evaluasi nilai akhir.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <div class="d-flex flex-column flex-sm-row flex-lg-column gap-2 justify-content-lg-end">
                            <a href="{{ route('login') }}" class="btn btn-cta-primary justify-content-center">
                                <i class="bi bi-box-arrow-in-right"></i> Masuk Sekarang
                            </a>
                            <a href="{{ route('pendaftaran.index') }}" class="btn btn-cta-light justify-content-center">
                                <i class="bi bi-card-checklist"></i> Portal Pendaftaran
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= FOOTER ================= -->
    <footer class="footer-landing">
        <div class="container">
            <div class="row gy-4 pb-4 border-bottom border-secondary border-opacity-25">
                <div class="col-lg-5">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <img src="{{ asset('images/logo-uniku.png') }}" alt="Logo UNIKU" style="height: 44px; width: auto;">
                        <div>
                            <h6 class="fw-bold text-white mb-0">FAKULTAS EKONOMI DAN BISNIS</h6>
                            <span class="text-white-50 fs-8">UNIVERSITAS KUNINGAN</span>
                        </div>
                    </div>
                    <p class="text-secondary fs-7 mb-3">
                        Sistem Informasi Pemantauan dan Penilaian Praktik Pengenalan Lapangan (PPL). Membangun sinergi akademik berintegritas antara kampus, dosen pembimbing, dan dunia usaha/industri.
                    </p>
                    <p class="text-secondary fs-8 mb-0">
                        <i class="bi bi-geo-alt-fill text-danger me-1"></i> Jl. Cut Nyak Dhien No. 36A, Cijoho, Kec. Kuningan, Kabupaten Kuningan, Jawa Barat 45513
                    </p>
                </div>

                <div class="col-6 col-lg-3 offset-lg-1">
                    <h6 class="text-white fw-bold mb-3">Tautan Langsung</h6>
                    <ul class="list-unstyled d-flex flex-column gap-2 fs-7">
                        <li><a href="{{ route('login') }}"><i class="bi bi-chevron-right fs-8 me-1"></i> Login Pengguna</a></li>
                        <li><a href="{{ route('pendaftaran.mahasiswa') }}"><i class="bi bi-chevron-right fs-8 me-1"></i> Pendaftaran Mahasiswa</a></li>
                        <li><a href="{{ route('pendaftaran.dpl') }}"><i class="bi bi-chevron-right fs-8 me-1"></i> Pendaftaran Calon DPL</a></li>
                        <li><a href="{{ route('pendaftaran.index') }}"><i class="bi bi-chevron-right fs-8 me-1"></i> Portal Seleksi Berkas</a></li>
                        <li><a href="{{ route('pedoman.index') }}"><i class="bi bi-chevron-right fs-8 me-1"></i> Buku Panduan / Pedoman PPL</a></li>
                    </ul>
                </div>

                <div class="col-6 col-lg-3">
                    <h6 class="text-white fw-bold mb-3">Program Studi FEB</h6>
                    <ul class="list-unstyled d-flex flex-column gap-2 fs-7">
                        <li><a href="https://uniku.ac.id" target="_blank"><i class="bi bi-arrow-up-right me-1"></i> Portal Universitas Kuningan</a></li>
                        <li><a href="#"><i class="bi bi-mortarboard me-1"></i> S1 Manajemen</a></li>
                        <li><a href="#"><i class="bi bi-mortarboard me-1"></i> S1 Akuntansi</a></li>
                        <li><a href="#"><i class="bi bi-mortarboard me-1"></i> S1 Bisnis Digital</a></li>
                        <li><a href="#"><i class="bi bi-mortarboard me-1"></i> Magang MBKM Rekognisi</a></li>
                    </ul>
                </div>
            </div>

            <div class="pt-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 fs-8 text-secondary">
                <div>
                    &copy; {{ date('Y') }} <strong>Fakultas Ekonomi dan Bisnis — Universitas Kuningan</strong>. All rights reserved.
                </div>
                <div class="d-flex gap-3">
                    <span>Developed by <a href="https://adi-muhamad.web.app/" target="_blank" class="text-white text-decoration-none border-bottom border-secondary pb-1">Dosen Sontoloyo</a> with <i class="bi bi-heart-fill text-danger mx-1"></i> for FEB UNIKU</span>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
