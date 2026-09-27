@extends('layouts.app')

@section('title', 'Monitoring & Kunjungan Lapangan DPL')

@section('content')
<div class="mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
        <div>
            <h4 class="fw-bold mb-1">📍 Monitoring & Kunjungan Lapangan DPL</h4>
            <p class="text-muted mb-0 fs-7">Kelola laporan bukti kunjungan lapangan DPL ke lokasi PPL Mitra (Penyerahan Awal & Penarikan Akhir Mahasiswa).</p>
        </div>
        <div>
            <a href="{{ route('dpl.monitoring.create') }}" class="btn btn-primary btn-touch text-white rounded-3 fw-semibold">
                + Tambah Laporan Kunjungan
            </a>
        </div>
    </div>
</div>

<!-- 4 Metric Cards Dashboard Kunjungan DPL -->
<div class="row g-3 mb-4">
    <!-- Stat 1: Total Kunjungan Lapangan -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-primary h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-primary fs-7 fw-bold">Total Kunjungan Lapangan</span>
                <span class="fs-4">📍</span>
            </div>
            <h3 class="fw-bold text-dark mb-1">
                {{ $statsSummary['total_kunjungan'] }} <span class="fs-6 text-muted font-normal">Laporan</span>
            </h3>
            <span class="fs-8 text-secondary">Terdaftar di sistem</span>
        </div>
    </div>

    <!-- Stat 2: Kunjungan 1 (Penyerahan Mahasiswa) -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-info h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-info fs-7 fw-bold">Kunjungan 1 (Penyerahan)</span>
                <span class="fs-4">🚀</span>
            </div>
            <h3 class="fw-bold text-dark mb-1">
                {{ $statsSummary['penyerahan_done'] }} <span class="fs-7 text-muted font-normal">/ {{ $statsSummary['total_kelompok'] }} Kelompok</span>
            </h3>
            <div class="fs-8 text-info fw-semibold">
                @if($statsSummary['penyerahan_done'] >= $statsSummary['total_kelompok'] && $statsSummary['total_kelompok'] > 0)
                    ✅ 100% Seluruh Kelompok Diserahkan
                @else
                    ⏳ Sisa {{ max(0, $statsSummary['total_kelompok'] - $statsSummary['penyerahan_done']) }} kelompok belum diserahkan
                @endif
            </div>
        </div>
    </div>

    <!-- Stat 3: Kunjungan 2 (Penarikan Mahasiswa) -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-warning h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-warning fs-7 fw-bold">Kunjungan 2 (Penarikan)</span>
                <span class="fs-4">🏁</span>
            </div>
            <h3 class="fw-bold text-dark mb-1">
                {{ $statsSummary['penarikan_done'] }} <span class="fs-7 text-muted font-normal">/ {{ $statsSummary['total_kelompok'] }} Kelompok</span>
            </h3>
            <div class="fs-8 text-warning fw-semibold">
                @if($statsSummary['penarikan_done'] >= $statsSummary['total_kelompok'] && $statsSummary['total_kelompok'] > 0)
                    ✅ 100% Seluruh Kelompok Ditarik
                @else
                    ⏳ Sisa {{ max(0, $statsSummary['total_kelompok'] - $statsSummary['penarikan_done']) }} kelompok belum ditarik
                @endif
            </div>
        </div>
    </div>

    <!-- Stat 4: Persetujuan / Approval Kelompok -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-start border-4 border-success h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-success fs-7 fw-bold">Approval Kelompok</span>
                <span class="fs-4">✅</span>
            </div>
            <h3 class="fw-bold text-dark mb-1">
                {{ $statsSummary['total_disetujui'] }} <span class="fs-6 text-muted font-normal">Disetujui</span>
            </h3>
            <div class="fs-8 text-secondary">
                <span class="text-success fw-semibold">✅ {{ $statsSummary['total_disetujui'] }} Disetujui</span> | 
                <span class="text-warning fw-semibold">⏳ {{ $statsSummary['total_pending'] }} Pending</span>
            </div>
        </div>
    </div>
</div>

<!-- Filter Card -->
<div class="card card-custom p-3 mb-4">
    <form action="{{ route('dpl.monitoring.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-12 col-md-4">
            <select name="kelompok_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">-- Semua Kelompok Bimbingan --</option>
                @foreach($kelompokList as $k)
                    <option value="{{ $k->id }}" {{ request('kelompok_id') == $k->id ? 'selected' : '' }}>
                        👥 {{ $k->nama_kelompok }} ({{ $k->mitra->nama_mitra ?? '-' }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-3">
            <select name="jenis" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">-- Semua Jenis Kunjungan --</option>
                <option value="penyerahan" {{ request('jenis') == 'penyerahan' ? 'selected' : '' }}>Kunjungan 1 - Penyerahan</option>
                <option value="penarikan" {{ request('jenis') == 'penarikan' ? 'selected' : '' }}>Kunjungan 2 - Penarikan</option>
                <option value="kunjungan_rutin" {{ request('jenis') == 'kunjungan_rutin' ? 'selected' : '' }}>Kunjungan Rutin</option>
            </select>
        </div>
        <div class="col-6 col-md-3">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">-- Status Approval Kelompok --</option>
                <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>✓ Disetujui</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Menunggu Approval</option>
            </select>
        </div>
        <div class="col-12 col-md-2 d-grid">
            <button type="submit" class="btn btn-sm btn-secondary fw-semibold">Filter</button>
        </div>
    </form>
</div>

<!-- Table Daftar Kunjungan Monitoring -->
<div class="card card-custom overflow-hidden mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 fs-7">
            <thead class="bg-light text-secondary">
                <tr>
                    <th class="ps-4">Kelompok & Mitra</th>
                    <th>Jenis Kunjungan</th>
                    <th>Tanggal & Waktu</th>
                    <th>Foto Dokumentasi</th>
                    <th>Catatan Kunjungan</th>
                    <th>Approval Kelompok</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($monitoringList as $m)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">👥 {{ $m->kelompok->nama_kelompok ?? '-' }}</div>
                            <div class="text-muted fs-8">🏢 {{ $m->kelompok->mitra->nama_mitra ?? '-' }}</div>
                        </td>
                        <td>
                            @if($m->jenis_kunjungan === 'penyerahan')
                                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-20 px-3 py-1">
                                    🚀 Kunjungan 1 (Penyerahan)
                                </span>
                            @elseif($m->jenis_kunjungan === 'penarikan')
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20 px-3 py-1">
                                    🏁 Kunjungan 2 (Penarikan)
                                </span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border px-3 py-1">
                                    📍 Kunjungan Rutin
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $m->tanggal_kunjungan->format('d/m/Y') }}</div>
                            @if($m->waktu_kunjungan)
                                <div class="text-muted fs-8">🕒 {{ \Carbon\Carbon::parse($m->waktu_kunjungan)->format('H:i') }} WIB</div>
                            @endif
                        </td>
                        <td>
                            @if($m->foto_kunjungan)
                                <a href="{{ asset('storage/' . $m->foto_kunjungan) }}" target="_blank" class="d-inline-block">
                                    <img src="{{ asset('storage/' . $m->foto_kunjungan) }}" alt="Foto Kunjungan" class="rounded border shadow-sm" style="width: 60px; height: 60px; object-fit: cover;">
                                </a>
                            @else
                                <span class="badge bg-light text-muted border">Tidak Ada Foto</span>
                            @endif
                        </td>
                        <td>
                            <div class="text-truncate" style="max-width: 250px;" title="{{ $m->catatan_kunjungan }}">
                                {{ $m->catatan_kunjungan }}
                            </div>
                        </td>
                        <td>
                            @if($m->disetujui_kelompok)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-3 py-1">
                                    ✓ Disetujui Kelompok
                                </span>
                                @if($m->tanggal_disetujui)
                                    <div class="fs-8 text-muted mt-1">{{ $m->tanggal_disetujui->format('d/m/Y H:i') }}</div>
                                @endif
                            @else
                                <span class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-20 px-3 py-1">
                                    ⏳ Menunggu Approval
                                </span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                <a href="{{ route('dpl.monitoring.edit', $m) }}" class="btn btn-sm btn-outline-primary">
                                    ✏️ Edit
                                </a>
                                <form action="{{ route('dpl.monitoring.destroy', $m) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan kunjungan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">🗑️ Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Belum ada laporan kunjungan monitoring DPL yang diunggah.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2 mt-3 px-1">
    <div class="text-muted fs-7">
        Menampilkan <strong>{{ $monitoringList->firstItem() ?? 0 }}</strong> – <strong>{{ $monitoringList->lastItem() ?? 0 }}</strong> dari <strong>{{ $monitoringList->total() }}</strong> Laporan Kunjungan
    </div>
    <div>
        {{ $monitoringList->links() }}
    </div>
</div>
@endsection
