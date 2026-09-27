@extends('layouts.app')

@section('title', 'Rekapitulasi Luaran Akhir PPL')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Rekapitulasi Luaran Akhir PPL Fakultas</h4>
        <p class="text-muted mb-0 fs-7">Pemantauan kelengkapan Laporan PDF & Link Video YouTube seluruh kelompok PPL.</p>
    </div>
</div>

<!-- Ringkasan Statistik Luaran Akhir Header Cards -->
<div class="row g-3 mb-4">
    <!-- Stat Card 1: Kelengkapan Luaran Kelompok -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-primary h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-primary fs-7 fw-bold">Status Luaran Kelompok</span>
                <span class="fs-4">📂</span>
            </div>
            <h3 class="fw-bold text-dark mb-1">
                {{ $statsSummary['kelompok_lengkap'] }} <span class="fs-6 text-muted font-normal">/ {{ $statsSummary['total_kelompok'] }} Kelompok</span>
            </h3>
            <div class="d-flex justify-content-between fs-8">
                <span class="text-success fw-semibold">✅ Lengkap: {{ $statsSummary['kelompok_lengkap'] }}</span>
                <span class="text-danger fw-semibold">⏳ Belum: {{ $statsSummary['kelompok_belum'] + $statsSummary['kelompok_parsial'] }}</span>
            </div>
        </div>
    </div>

    <!-- Stat Card 2: Laporan PDF Terkumpul -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-info h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-info fs-7 fw-bold">Laporan PDF Terkumpul</span>
                <span class="fs-4">📄</span>
            </div>
            <h3 class="fw-bold text-dark mb-1">
                {{ $statsSummary['pdf_terkumpul'] }} <span class="fs-6 text-muted font-normal">/ {{ $statsSummary['total_kelompok'] }} PDF</span>
            </h3>
            <div class="d-flex justify-content-between fs-8">
                <span class="text-success fw-semibold">✅ Upload: {{ $statsSummary['pdf_terkumpul'] }}</span>
                <span class="text-danger fw-semibold">⏳ Belum: {{ $statsSummary['pdf_belum'] }}</span>
            </div>
        </div>
    </div>

    <!-- Stat Card 3: Link Video YouTube -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-danger h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-danger fs-7 fw-bold">Video YouTube PPL</span>
                <span class="fs-4">🎬</span>
            </div>
            <h3 class="fw-bold text-dark mb-1">
                {{ $statsSummary['video_terkumpul'] }} <span class="fs-6 text-muted font-normal">/ {{ $statsSummary['total_kelompok'] }} Video</span>
            </h3>
            <div class="d-flex justify-content-between fs-8">
                <span class="text-success fw-semibold">✅ Isi Link: {{ $statsSummary['video_terkumpul'] }}</span>
                <span class="text-danger fw-semibold">⏳ Belum: {{ $statsSummary['video_belum'] }}</span>
            </div>
        </div>
    </div>

    <!-- Stat Card 4: Persentase Progres Kelengkapan -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-success h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-success fs-7 fw-bold">Progres Kelengkapan</span>
                <span class="fs-4">📈</span>
            </div>
            <h3 class="fw-bold text-success mb-1">
                {{ $statsSummary['persentase_progres'] }}%
            </h3>
            <span class="text-muted fs-8">{{ $statsSummary['kelompok_lengkap'] }} dari {{ $statsSummary['total_kelompok'] }} Kelompok Terpenuhi</span>
        </div>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="card card-custom p-3 mb-4">
    <form action="{{ route('admin.luaran.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-12 col-md-6">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari Nama Kelompok, Mitra, DPL, atau Ketua Kelompok..." value="{{ request('search') }}">
        </div>
        <div class="col-6 col-md-3">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">-- Semua Status Luaran --</option>
                <option value="lengkap" {{ request('status') == 'lengkap' ? 'selected' : '' }}>✅ Lengkap (PDF & Video)</option>
                <option value="parsial" {{ request('status') == 'parsial' ? 'selected' : '' }}>⏳ Parsial (Salah Satu)</option>
                <option value="belum" {{ request('status') == 'belum' ? 'selected' : '' }}>⚠️ Belum Unggah</option>
            </select>
        </div>
        <div class="col-6 col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-secondary fw-semibold w-100">Filter Data</button>
            <a href="{{ route('admin.luaran.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        </div>
    </form>
</div>

<!-- Table Rekapitulasi Luaran Akhir PPL -->
<div class="card card-custom overflow-hidden mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 fs-7">
            <thead class="bg-light text-secondary">
                <tr>
                    <th class="ps-4">Nama Kelompok</th>
                    <th>Mitra</th>
                    <th>DPL Pembimbing</th>
                    <th>Laporan PDF (Max 10MB)</th>
                    <th>Video YouTube</th>
                    <th class="text-end pe-4">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kelompokList as $kelompok)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">{{ $kelompok->nama_kelompok }}</div>
                            <div class="text-muted fs-8">👑 {{ $kelompok->ketua->nama_lengkap ?? '-' }}</div>
                        </td>
                        <td>{{ $kelompok->mitra->nama_mitra ?? '-' }}</td>
                        <td>{{ $kelompok->dpl->nama_lengkap ?? '-' }}</td>
                        <td>
                            @if($kelompok->luaran && $kelompok->luaran->file_laporan_pdf)
                                <a href="{{ route('luaran.pdf.download', $kelompok->luaran) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    📄 Download PDF
                                </a>
                            @else
                                <span class="badge bg-light text-muted border">Belum Upload</span>
                            @endif
                        </td>
                        <td>
                            @if($kelompok->luaran && $kelompok->luaran->url_video)
                                <a href="{{ $kelompok->luaran->url_video }}" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                    🎬 Video YouTube
                                </a>
                            @else
                                <span class="badge bg-light text-muted border">Belum Isi Link</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            @if($kelompok->luaran && $kelompok->luaran->file_laporan_pdf && $kelompok->luaran->url_video)
                                <span class="badge bg-success">Lengkap</span>
                            @elseif($kelompok->luaran && ($kelompok->luaran->file_laporan_pdf || $kelompok->luaran->url_video))
                                <span class="badge bg-info text-dark">Parsial</span>
                            @else
                                <span class="badge bg-warning text-dark">Belum Unggah</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Belum ada data kelompok PPL yang sesuai.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2 mt-3 px-1">
    <div class="text-muted fs-7">
        Menampilkan <strong>{{ $kelompokList->firstItem() ?? 0 }}</strong> – <strong>{{ $kelompokList->lastItem() ?? 0 }}</strong> dari <strong>{{ $kelompokList->total() }}</strong> Kelompok PPL
    </div>
    <div>
        {{ $kelompokList->links() }}
    </div>
</div>
@endsection
