@extends('layouts.app')

@section('title', 'Plotting Kelompok PPL')

@section('content')
<div class="mb-4">
    <div class="mb-3">
        <h4 class="fw-bold mb-1">Plotting & Pemetaan Kelompok PPL</h4>
        <p class="text-muted mb-0 fs-7">Pemetaan penempatan antara <strong>Kelompok PPL</strong>, <strong>Mitra Instansi</strong>, <strong>Dosen Pembimbing (DPL)</strong>, dan <strong>Mahasiswa Anggota</strong> (1–10 mahasiswa per kelompok).</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.plotting.pdf', request()->query()) }}" target="_blank" class="btn btn-outline-danger btn-touch rounded-3 fw-semibold">
            📄 Cetak PDF Report
        </a>
        <a href="{{ route('admin.plotting.export-excel', request()->query()) }}" class="btn btn-outline-success btn-touch rounded-3 fw-semibold">
            📊 Export Excel
        </a>
    </div>
</div>

<!-- Search Card -->
<div class="card card-custom p-3 mb-4">
    <form action="{{ route('admin.plotting.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-12 col-md-9">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari Nama Kelompok, Mitra, atau Nama DPL..." value="{{ request('search') }}">
        </div>
        <div class="col-12 col-md-3 d-grid">
            <button type="submit" class="btn btn-sm btn-secondary fw-semibold">Cari Plotting</button>
        </div>
    </form>
</div>

<!-- Table Plotting Kelompok -->
<div class="card card-custom overflow-hidden mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 fs-7">
            <thead class="bg-light text-secondary">
                <tr>
                    <th class="ps-4">Nama Kelompok</th>
                    <th>Mitra Penempatan</th>
                    <th>DPL Pembimbing</th>
                    <th>Anggota Mahasiswa (1-10 Mhs)</th>
                    <th class="text-end pe-4">Aksi Plotting</th>
                </tr>
            </thead>
            <tbody>
                @forelse($plottingList as $k)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">👥 {{ $k->nama_kelompok }}</div>
                            <div class="text-muted fs-8">Akun: <code>{{ $k->ketua->username ?? '-' }}</code></div>
                        </td>
                        <td>
                            @if($k->mitra)
                                <div class="fw-bold text-dark">🏢 {{ $k->mitra->nama_mitra }}</div>
                                <div class="text-muted fs-8">Kategori: {{ $k->mitra->kategori }}</div>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger border">⚠️ Belum Diplotkan</span>
                            @endif
                        </td>
                        <td>
                            @if($k->dpl)
                                <div class="fw-bold text-dark">👨‍🏫 {{ $k->dpl->nama_lengkap }}</div>
                                <div class="text-muted fs-8">NIP/NIDN: {{ $k->dpl->nip_nidn ?? '-' }}</div>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger border">⚠️ Belum Diplotkan</span>
                            @endif
                        </td>
                        <td>
                            @if($k->anggota->count() > 0)
                                @php $c = $k->anggota->count(); @endphp
                                <div class="fw-semibold text-dark mb-1">
                                    @if($c > 15)
                                        <span class="badge bg-danger px-2 py-1 me-1">🔥 {{ $c }} Mahasiswa</span>
                                    @elseif($c > 10)
                                        <span class="badge bg-warning text-dark px-2 py-1 me-1">⚠️ {{ $c }} Mahasiswa</span>
                                    @else
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-2 py-1 me-1">🟢 {{ $c }} Mahasiswa</span>
                                    @endif
                                </div>
                                <div class="fs-8 text-secondary">
                                    @foreach($k->anggota->take(3) as $mhs)
                                        <div>• {{ $mhs->nama }} (<code>{{ $mhs->nim }}</code>)</div>
                                    @endforeach
                                    @if($k->anggota->count() > 3)
                                        <div class="fst-italic text-muted">+ {{ $k->anggota->count() - 3 }} mahasiswa lainnya...</div>
                                    @endif
                                </div>
                            @else
                                <span class="badge bg-warning bg-opacity-10 text-warning border">⚠️ Belum Ada Mahasiswa</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.plotting.edit', $k) }}" class="btn btn-sm btn-success fw-semibold">
                                🗺️ Edit Plotting
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Belum ada data kelompok PPL untuk diplotkan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2 mt-3 px-1">
    <div class="text-muted fs-7">
        Menampilkan <strong>{{ $plottingList->firstItem() ?? 0 }}</strong> – <strong>{{ $plottingList->lastItem() ?? 0 }}</strong> dari <strong>{{ $plottingList->total() }}</strong> Kelompok
    </div>
    <div>
        {{ $plottingList->links() }}
    </div>
</div>
@endsection
