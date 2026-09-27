@extends('layouts.app')

@section('title', 'Dashboard Kelompok PPL')

@section('content')

@if(!$kelompok)
    <div class="card card-custom p-4 text-center text-muted">
        <div class="mb-3">
            <span class="fs-1">⚠️</span>
        </div>
        <h5 class="fw-bold text-dark">Akun Belum Terhubung dengan Kelompok Active</h5>
        <p class="fs-7 text-muted mb-0">Anda belum terdaftar dalam kelompok PPL aktif. Silakan hubungi Administrator Unit PPL FEB UNIKU.</p>
    </div>
@else

    <!-- Welcome Header & Quick Action Buttons -->
    <div class="card card-custom p-4 mb-4 bg-primary text-white border-0 shadow-sm overflow-hidden position-relative">
        <div class="row align-items-center position-relative z-1">
            <div class="col-12 col-lg-7 mb-3 mb-lg-0">
                <span class="badge bg-white text-primary px-3 py-2 rounded-pill fw-semibold mb-2 fs-8">
                    👥 {{ $kelompok->nama_kelompok }} — TA {{ $kelompok->tahun_akademik }}
                </span>
                <h3 class="fw-bold mb-1">Selamat Datang, {{ $ketua->nama_lengkap }}</h3>
                <p class="text-white-50 mb-0 fs-7">
                    Pantau aktivitas harian magang, status persetujuan logbook, serta pelaporan luaran PPL dalam satu panel terintegrasi.
                </p>
            </div>
            <div class="col-12 col-lg-5 text-lg-end">
                <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                    <a href="{{ route('ketua.logbook.create') }}" class="btn btn-light text-primary fw-bold rounded-3 btn-touch fs-7">
                        ✏️ Input Logbook Hari Ini
                    </a>
                    <a href="{{ route('ketua.luaran.index') }}" class="btn btn-outline-light text-white fw-semibold rounded-3 btn-touch fs-7">
                        📁 Upload Luaran Akhir
                    </a>
                    <a href="{{ route('ketua.logbook.pdf') }}" class="btn btn-danger text-white fw-semibold rounded-3 btn-touch fs-7" title="Cetak Laporan Logbook PDF">
                        📄 Cetak PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Logbook Hari Ini -->
    <div class="mb-4">
        @if($todayLogbook)
            <div class="alert alert-success d-flex align-items-center justify-content-between rounded-3 border-0 shadow-sm p-3 mb-0" role="alert">
                <div class="d-flex align-items-center gap-2">
                    <span class="fs-4">✅</span>
                    <div>
                        <strong class="d-block text-success fs-7">Logbook Hari Ini ({{ now()->translatedFormat('d F Y') }}) Sudah Terisi</strong>
                        <span class="fs-8 text-muted">Aktivitas jam {{ \Carbon\Carbon::parse($todayLogbook->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($todayLogbook->waktu_selesai)->format('H:i') }} WIB telah berhasil dicatat.</span>
                    </div>
                </div>
                <a href="{{ route('ketua.logbook.show', $todayLogbook) }}" class="btn btn-sm btn-success px-3 rounded-2 fw-semibold text-nowrap">
                    Lihat Detail
                </a>
            </div>
        @else
            <div class="alert alert-warning d-flex align-items-center justify-content-between rounded-3 border-0 shadow-sm p-3 mb-0" role="alert">
                <div class="d-flex align-items-center gap-2">
                    <span class="fs-4">⚡</span>
                    <div>
                        <strong class="d-block text-dark fs-7">Logbook Hari Ini ({{ now()->translatedFormat('d F Y') }}) Belum Diisi</strong>
                        <span class="fs-8 text-muted">Jangan lupa untuk mengisi jurnal aktivitas kegiatan magang hari ini sebelum batas waktu berakhir.</span>
                    </div>
                </div>
                <a href="{{ route('ketua.logbook.create') }}" class="btn btn-sm btn-warning text-dark px-3 rounded-2 fw-bold text-nowrap">
                    + Input Sekarang
                </a>
            </div>
        @endif
    </div>

    <!-- Metric Executive Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fs-8 text-uppercase fw-bold">Total Logbook</span>
                    <span class="badge bg-primary bg-opacity-10 text-primary p-2 rounded-circle">📖</span>
                </div>
                <h3 class="fw-bold mb-1 text-dark">{{ $totalLogbook }}</h3>
                <span class="fs-8 text-muted">Entri kegiatan harian</span>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fs-8 text-uppercase fw-bold">Approved PIC Mitra</span>
                    <span class="badge bg-success bg-opacity-10 text-success p-2 rounded-circle">🏢</span>
                </div>
                <h3 class="fw-bold mb-1 text-success">{{ $approvedMitraCount }} <span class="fs-7 text-muted fw-normal">/ {{ $totalLogbook }}</span></h3>
                <span class="fs-8 text-muted">
                    {{ $totalLogbook > 0 ? round(($approvedMitraCount / $totalLogbook) * 100) : 0 }}% Terverifikasi Mitra
                </span>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fs-8 text-uppercase fw-bold">Approved DPL</span>
                    <span class="badge bg-info bg-opacity-10 text-info p-2 rounded-circle">👨‍🏫</span>
                </div>
                <h3 class="fw-bold mb-1 text-info">{{ $approvedDplCount }} <span class="fs-7 text-muted fw-normal">/ {{ $totalLogbook }}</span></h3>
                <span class="fs-8 text-muted">
                    {{ $totalLogbook > 0 ? round(($approvedDplCount / $totalLogbook) * 100) : 0 }}% Terverifikasi DPL
                </span>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-custom p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fs-8 text-uppercase fw-bold">Luaran Akhir PPL</span>
                    <span class="badge bg-warning bg-opacity-10 text-warning p-2 rounded-circle">📁</span>
                </div>
                <h3 class="fw-bold mb-1 fs-6">
                    @if($luaran)
                        <span class="badge bg-success text-white">Sudah Diunggah</span>
                    @else
                        <span class="badge bg-secondary text-white">Belum Diunggah</span>
                    @endif
                </h3>
                <span class="fs-8 text-muted">
                    @if($luaran)
                        Berkas laporan akhir tersimpan
                    @else
                        Wajib diunggah sebelum penarikan
                    @endif
                </span>
            </div>
        </div>
    </div>

    <!-- Row: Informasi Penempatan & Pembimbing -->
    <div class="row g-3 mb-4">
        <!-- Card Detail Instansi Mitra -->
        <div class="col-12 col-lg-6">
            <div class="card card-custom p-4 h-100">
                <div class="d-flex align-items-center gap-2 mb-3 border-bottom pb-2">
                    <span class="fs-5">🏢</span>
                    <h6 class="fw-bold mb-0 text-dark">Informasi Penempatan Mitra Magang</h6>
                </div>
                <table class="table table-borderless table-sm mb-0 fs-7">
                    <tr>
                        <td class="text-muted" style="width: 35%;">Nama Instansi</td>
                        <td class="fw-bold text-dark">: {{ $kelompok->mitra->nama_mitra ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kategori</td>
                        <td>: <span class="badge bg-light text-dark border fs-8">{{ $kelompok->mitra->kategori ?? '-' }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Alamat Mitra</td>
                        <td class="text-dark">: {{ $kelompok->mitra->alamat ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Pembimbing / PIC</td>
                        <td class="fw-semibold text-primary">: {{ $kelompok->mitra->picUser->nama_lengkap ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kontak PIC Mitra</td>
                        <td>: 
                            @if($kelompok->mitra->picUser->no_hp ?? false)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $kelompok->mitra->picUser->no_hp) }}" target="_blank" class="text-decoration-none fw-semibold text-success fs-8">
                                    💬 {{ $kelompok->mitra->picUser->no_hp }} (WhatsApp)
                                </a>
                            @else
                                <span class="text-muted fs-8">-</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Card Detail DPL Pembimbing -->
        <div class="col-12 col-lg-6">
            <div class="card card-custom p-4 h-100">
                <div class="d-flex align-items-center gap-2 mb-3 border-bottom pb-2">
                    <span class="fs-5">👨‍🏫</span>
                    <h6 class="fw-bold mb-0 text-dark">Dosen Pembimbing Lapangan (DPL)</h6>
                </div>
                <table class="table table-borderless table-sm mb-0 fs-7">
                    <tr>
                        <td class="text-muted" style="width: 35%;">Nama DPL</td>
                        <td class="fw-bold text-dark">: {{ $kelompok->dpl->nama_lengkap ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">NIP / NIDN</td>
                        <td class="text-dark">: {{ $kelompok->dpl->nip_nidn ?? $kelompok->dpl->username ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kontak WhatsApp</td>
                        <td>: 
                            @if($kelompok->dpl->no_hp ?? false)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $kelompok->dpl->no_hp) }}" target="_blank" class="text-decoration-none fw-semibold text-success fs-8">
                                    💬 {{ $kelompok->dpl->no_hp }} (WhatsApp)
                                </a>
                            @else
                                <span class="text-muted fs-8">-</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tahun Akademik</td>
                        <td class="text-dark">: {{ $kelompok->tahun_akademik }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jumlah Anggota</td>
                        <td class="fw-semibold text-dark">: {{ $kelompok->anggota->count() }} Mahasiswa</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Row: Logbook Terakhir & Anggota Kelompok -->
    <div class="row g-3">
        <!-- Logbook Terbaru -->
        <div class="col-12 col-lg-7">
            <div class="card card-custom p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fs-5">📝</span>
                        <h6 class="fw-bold mb-0 text-dark">Aktivitas Logbook Terbaru</h6>
                    </div>
                    <a href="{{ route('ketua.logbook.index') }}" class="fs-8 text-primary fw-semibold text-decoration-none">
                        Lihat Semua →
                    </a>
                </div>

                @if($recentLogbooks->isEmpty())
                    <div class="text-center py-4 text-muted fs-7">
                        Belum ada entri logbook kegiatan harian yang dicatat.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 fs-7">
                            <thead class="bg-light text-secondary">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Kegiatan</th>
                                    <th>Approval</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentLogbooks as $logbook)
                                    <tr>
                                        <td class="text-nowrap">
                                            <strong class="text-dark">{{ $logbook->tanggal->format('d/m/Y') }}</strong>
                                            @if($logbook->terlambat)
                                                <span class="badge bg-warning text-dark fs-9 d-block mt-1">Terlambat</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 220px;" title="{{ $logbook->deskripsi_kegiatan }}">
                                                {{ $logbook->deskripsi_kegiatan }}
                                            </div>
                                            <span class="fs-8 text-muted">
                                                🕒 {{ \Carbon\Carbon::parse($logbook->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($logbook->waktu_selesai)->format('H:i') }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="mb-1">
                                                @if($logbook->dilihat_mitra)
                                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 fs-9">✓ Mitra</span>
                                                @else
                                                    <span class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-20 fs-9">Mitra</span>
                                                @endif
                                            </div>
                                            <div>
                                                @if($logbook->dilihat_dpl)
                                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 fs-9">✓ DPL</span>
                                                @else
                                                    <span class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-20 fs-9">DPL</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('ketua.logbook.show', $logbook) }}" class="btn btn-sm btn-outline-primary fs-8 py-1 px-2">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Anggota Kelompok -->
        <div class="col-12 col-lg-5">
            <div class="card card-custom p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fs-5">👥</span>
                        <h6 class="fw-bold mb-0 text-dark">Anggota Kelompok PPL</h6>
                    </div>
                    <span class="badge bg-secondary px-2 py-1 fs-8">{{ $kelompok->anggota->count() }} Orang</span>
                </div>

                @if($kelompok->anggota->isEmpty())
                    <div class="text-center py-4 text-muted fs-7">
                        Belum ada anggota mahasiswa yang diplotkan ke dalam kelompok ini.
                    </div>
                @else
                    <div class="list-group list-group-flush fs-7">
                        @foreach($kelompok->anggota as $mhs)
                            <div class="list-group-item px-0 py-2 border-bottom-dashed">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <strong class="text-dark d-block mb-0">{{ $mhs->nama }}</strong>
                                        <span class="fs-8 text-muted">NIM: {{ $mhs->nim }} | {{ $mhs->prodi ?? 'FEB' }} ({{ $mhs->konsentrasi ?? '-' }})</span>
                                    </div>
                                    <span class="badge bg-light text-dark border fs-8">
                                        {{ $mhs->jenis_kelamin === 'L' ? '👨 L' : '👩 P' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

@endif

@endsection
