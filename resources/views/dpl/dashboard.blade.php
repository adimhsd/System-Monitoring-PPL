@extends('layouts.app')

@section('title', 'Dashboard DPL Pembimbing')

@section('content')

<!-- Welcome Header & DPL Status -->
<div class="card card-custom p-4 mb-4 bg-primary text-white border-0 shadow-sm overflow-hidden position-relative">
    <div class="row align-items-center position-relative z-1">
        <div class="col-12 col-lg-8 mb-3 mb-lg-0">
            <span class="badge bg-white text-primary px-3 py-2 rounded-pill fw-semibold mb-2 fs-8">
                👨‍🏫 Dosen Pembimbing Lapangan (DPL)
            </span>
            <h3 class="fw-bold mb-1">Selamat Datang, {{ $dpl->nama_lengkap }}</h3>
            <p class="text-white-50 mb-0 fs-7">
                NIP / NIDN: {{ $dpl->nip_nidn ?? $dpl->username }} | Unit Kerja: Fakultas Ekonomi dan Bisnis Universitas Kuningan
            </p>
        </div>
        <div class="col-12 col-lg-4 text-lg-end">
            <span class="text-white-50 fs-8 d-block mb-1">Beban Bimbingan Mahasiswa:</span>
            <span class="badge {{ $totalMahasiswa > 20 ? 'bg-danger text-white' : ($totalMahasiswa > 10 ? 'bg-warning text-dark' : 'bg-success text-white') }} fs-6 px-3 py-2 shadow-sm">
                👥 {{ $totalMahasiswa }} / Maks 30 Mahasiswa
            </span>
        </div>
    </div>
</div>

<!-- Alert Widget Logbook Menunggu Approval -->
<div class="mb-4">
    @if($totalPendingApproval > 0)
        <div class="alert alert-warning d-flex align-items-center justify-content-between rounded-3 border-0 shadow-sm p-3 mb-0" role="alert">
            <div class="d-flex align-items-center gap-2">
                <span class="fs-4">⚠️</span>
                <div>
                    <strong class="d-block text-dark fs-7">Ada {{ $totalPendingApproval }} Logbook Harian Menunggu Approval Anda</strong>
                    <span class="fs-8 text-muted">Mohon lakukan pengecekan dan verifikasi jurnal kegiatan mahasiswa bimbingan Anda.</span>
                </div>
            </div>
            <a href="{{ route('dpl.logbook.index', ['status_dilihat' => 'belum']) }}" class="btn btn-sm btn-warning text-dark px-3 rounded-2 fw-bold text-nowrap">
                ⚡ Review & Approve Logbook
            </a>
        </div>
    @else
        <div class="alert alert-success d-flex align-items-center justify-content-between rounded-3 border-0 shadow-sm p-3 mb-0" role="alert">
            <div class="d-flex align-items-center gap-2">
                <span class="fs-4">✅</span>
                <div>
                    <strong class="d-block text-success fs-7">Semua Logbook Bimbingan Sudah Di-Approve</strong>
                    <span class="fs-8 text-muted">Tidak ada jurnal harian yang tertunda. Semua aktivitas mahasiswa bimbingan Anda telah diverifikasi.</span>
                </div>
            </div>
            <a href="{{ route('dpl.logbook.index') }}" class="btn btn-sm btn-outline-success px-3 rounded-2 fw-semibold text-nowrap">
                Lihat Semua Logbook
            </a>
        </div>
    @endif
</div>

<!-- Metric Executive Summary Cards (4 Cards Grid) -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fs-8 text-uppercase fw-bold">Kelompok Bimbingan</span>
                <span class="badge bg-primary bg-opacity-10 text-primary p-2 rounded-circle">👥</span>
            </div>
            <h3 class="fw-bold mb-1 text-dark">{{ $totalKelompok }}</h3>
            <span class="fs-8 text-muted">Kelompok PPL aktif</span>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fs-8 text-uppercase fw-bold">Total Mahasiswa</span>
                <span class="badge bg-info bg-opacity-10 text-info p-2 rounded-circle">👨‍🎓</span>
            </div>
            <h3 class="fw-bold mb-1 text-info">{{ $totalMahasiswa }} <span class="fs-7 text-muted fw-normal">/ 30</span></h3>
            <span class="fs-8 text-muted">Mahasiswa bimbingan DPL</span>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fs-8 text-uppercase fw-bold">Pending Approval</span>
                <span class="badge bg-warning bg-opacity-10 text-warning p-2 rounded-circle">⏳</span>
            </div>
            <h3 class="fw-bold mb-1 text-warning">{{ $totalPendingApproval }}</h3>
            <span class="fs-8 text-muted">Logbook butuh verifikasi</span>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fs-8 text-uppercase fw-bold">Penilaian DPL (40%)</span>
                <span class="badge bg-success bg-opacity-10 text-success p-2 rounded-circle">📝</span>
            </div>
            <h3 class="fw-bold mb-1 text-success">{{ $penilaianDoneCount }} <span class="fs-7 text-muted fw-normal">/ {{ $totalKelompok }}</span></h3>
            <span class="fs-8 text-muted">
                {{ $totalKelompok > 0 ? round(($penilaianDoneCount / $totalKelompok) * 100) : 0 }}% Kelompok dinilai
            </span>
        </div>
    </div>
</div>

<!-- Daftar Kelompok Bimbingan Cards -->
<div class="d-flex align-items-center justify-content-between mb-3">
    <h5 class="fw-bold text-dark mb-0">👥 Daftar Kelompok Bimbingan PPL</h5>
    <a href="{{ route('dpl.logbook.index') }}" class="btn btn-sm btn-outline-primary rounded-3 fs-8 fw-semibold">
        📘 Kelola Semua Logbook →
    </a>
</div>

@if($kelompokBimbingan->isEmpty())
    <div class="card card-custom p-4 text-center text-muted mb-4">
        Belum ada kelompok PPL yang diplotkan untuk Anda saat ini.
    </div>
@else
    <div class="row g-3 mb-4">
        @foreach($kelompokBimbingan as $kelompok)
            @php
                $pendingInKelompok = $kelompok->kegiatanHarian->where('dilihat_dpl', false)->count();
                $totalLogbookInKelompok = $kelompok->kegiatanHarian->count();
                $isPenilaianComplete = $kelompok->penilaian && $kelompok->penilaian->dpl_nilai_total !== null;
            @endphp
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card card-custom p-4 h-100 shadow-sm border-start border-4 border-primary">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="fw-bold text-primary mb-0 fs-6">{{ $kelompok->nama_kelompok }}</h6>
                        <span class="badge bg-light text-dark border fs-8">TA {{ $kelompok->tahun_akademik }}</span>
                    </div>

                    <div class="fs-7 text-secondary mb-3">
                        <div class="mb-1">🏢 <strong>Mitra:</strong> {{ $kelompok->mitra->nama_mitra ?? '-' }} ({{ $kelompok->mitra->kategori ?? '-' }})</div>
                        <div class="mb-1">👨‍💼 <strong>PIC Mitra:</strong> {{ $kelompok->mitra->picUser->nama_lengkap ?? '-' }}</div>
                        <div class="mb-1">👤 <strong>Akun:</strong> {{ $kelompok->ketua->username ?? '-' }}</div>
                        <div>👥 <strong>Anggota:</strong> {{ $kelompok->anggota->count() }} Mahasiswa</div>
                    </div>

                    <!-- Progress Status Badges -->
                    <div class="bg-light p-2 rounded-3 mb-3 fs-8">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Total Logbook:</span>
                            <span class="fw-bold text-dark">{{ $totalLogbookInKelompok }} entri</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Approval DPL:</span>
                            @if($pendingInKelompok > 0)
                                <span class="badge bg-warning text-dark">{{ $pendingInKelompok }} Belum Di-approve</span>
                            @else
                                <span class="badge bg-success">✓ 100% Approved</span>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Penilaian DPL (40%):</span>
                            @if($isPenilaianComplete)
                                <span class="badge bg-success">✓ Sudah Dinilai</span>
                            @else
                                <span class="badge bg-secondary">Belum Dinilai</span>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex flex-wrap gap-2 pt-2 border-top mt-auto">
                        <a href="{{ route('dpl.logbook.index', ['kelompok_id' => $kelompok->id]) }}" class="btn btn-sm btn-primary flex-fill fw-semibold rounded-2 fs-8">
                            📘 Lihat Logbook
                        </a>
                        <a href="{{ route('dpl.penilaian.edit', $kelompok) }}" class="btn btn-sm btn-outline-success flex-fill fw-semibold rounded-2 fs-8">
                            📝 Penilaian
                        </a>
                        <a href="{{ route('dpl.logbook.pdf', $kelompok) }}" class="btn btn-sm btn-outline-danger px-2 rounded-2 fs-8" title="Cetak PDF Logbook">
                            📄 PDF
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

<!-- Row: Logbook Menunggu Approval Terbaru -->
@if(!$recentPendingLogbooks->isEmpty())
    <div class="card card-custom p-4 mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
            <div class="d-flex align-items-center gap-2">
                <span class="fs-5">⏳</span>
                <h6 class="fw-bold mb-0 text-dark">Logbook Menunggu Approval DPL Terbaru</h6>
            </div>
            <a href="{{ route('dpl.logbook.index', ['status_dilihat' => 'belum']) }}" class="fs-8 text-primary fw-semibold text-decoration-none">
                Approve Semua →
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 fs-7">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th>Kelompok</th>
                        <th>Tanggal</th>
                        <th>Kegiatan</th>
                        <th>PIC Mitra</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentPendingLogbooks as $logbook)
                        <tr>
                            <td class="fw-bold text-dark">
                                {{ $logbook->kelompok->nama_kelompok ?? '-' }}
                                <div class="fs-8 text-muted font-normal">{{ $logbook->kelompok->mitra->nama_mitra ?? '-' }}</div>
                            </td>
                            <td class="text-nowrap">
                                <strong>{{ $logbook->tanggal->format('d/m/Y') }}</strong>
                                <span class="fs-8 text-muted d-block">🕒 {{ \Carbon\Carbon::parse($logbook->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($logbook->waktu_selesai)->format('H:i') }}</span>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 250px;" title="{{ $logbook->deskripsi_kegiatan }}">
                                    {{ $logbook->deskripsi_kegiatan }}
                                </div>
                            </td>
                            <td>
                                @if($logbook->dilihat_mitra)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 fs-9">✓ Approved</span>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-20 fs-9">Belum</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('dpl.logbook.show', $logbook) }}" class="btn btn-sm btn-primary fs-8 py-1 px-3 rounded-2">
                                    Detail & Approve
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

@endsection
