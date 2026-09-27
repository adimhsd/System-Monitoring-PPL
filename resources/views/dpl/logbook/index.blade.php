@extends('layouts.app')

@section('title', 'Pemantauan Logbook DPL')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Pemantauan Logbook Kelompok Bimbingan DPL</h4>
    <p class="text-muted mb-0 fs-7">Tinjau dan approve logbook kegiatan harian mahasiswa magang PPL.</p>
</div>

<div class="card card-custom p-3 mb-4">
    <form action="{{ route('dpl.logbook.index') }}" method="GET" class="row g-2">
        <div class="col-12 col-md-5">
            <select name="kelompok_id" class="form-select fs-7">
                <option value="">-- Semua Kelompok Bimbingan --</option>
                @foreach($kelompokList as $k)
                    <option value="{{ $k->id }}" {{ request('kelompok_id') == $k->id ? 'selected' : '' }}>
                        {{ $k->nama_kelompok }} (Mitra: {{ $k->mitra->nama_mitra ?? '-' }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-4">
            <select name="status_dilihat" class="form-select fs-7">
                <option value="">-- Semua Status Approval --</option>
                <option value="belum" {{ request('status_dilihat') == 'belum' ? 'selected' : '' }}>Belum Di-Approve DPL</option>
                <option value="sudah" {{ request('status_dilihat') == 'sudah' ? 'selected' : '' }}>Sudah Di-Approve DPL</option>
            </select>
        </div>
        <div class="col-12 col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-outline-primary fs-7 w-100">Filter</button>
            @if(request('kelompok_id'))
                <a href="{{ route('dpl.logbook.pdf', request('kelompok_id')) }}" class="btn btn-outline-danger fs-7 text-nowrap" title="Cetak Laporan Logbook PDF">📄 PDF</a>
            @endif
            <a href="{{ route('dpl.logbook.index') }}" class="btn btn-outline-secondary fs-7">Reset</a>
        </div>
    </form>
</div>

<div class="card card-custom overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 fs-7">
            <thead class="bg-light text-secondary">
                <tr>
                    <th class="ps-4">Tanggal</th>
                    <th>Kelompok & Mitra</th>
                    <th>Uraian Kegiatan</th>
                    <th>Status Approval</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logbookList as $logbook)
                    <tr>
                        <td class="ps-4 fw-semibold text-dark">
                            {{ $logbook->tanggal->translatedFormat('d/m/Y') }}
                            @if($logbook->terlambat)
                                <span class="badge bg-warning text-dark d-block mt-1">⚠️ Terlambat</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-primary">{{ $logbook->kelompok->nama_kelompok }}</div>
                            <div class="text-muted fs-8">🏢 {{ $logbook->kelompok->mitra->nama_mitra ?? '-' }}</div>
                        </td>
                        <td class="text-truncate" style="max-width: 300px;">
                            {{ $logbook->deskripsi_kegiatan }}
                        </td>
                        <td>
                            @if($logbook->dilihat_dpl)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                    ✓ Approved ({{ $logbook->dilihat_dpl_at->format('d/m H:i') }})
                                </span>
                            @else
                                <span class="badge bg-warning bg-opacity-10 text-dark border border-warning">
                                    Belum Di-Approve DPL
                                </span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('dpl.logbook.pdf', $logbook->kelompok_id) }}" class="btn btn-sm btn-outline-danger me-1" title="Cetak Laporan Logbook PDF">
                                📄 PDF
                            </a>
                            <a href="{{ route('dpl.logbook.show', $logbook) }}" class="btn btn-sm btn-outline-primary">
                                Detail Logbook
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Belum ada entri logbook harian.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $logbookList->links() }}
</div>
@endsection
