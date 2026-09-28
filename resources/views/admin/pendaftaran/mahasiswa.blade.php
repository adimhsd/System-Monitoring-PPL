@extends('layouts.app')

@section('title', 'Pendaftaran Mahasiswa PPL')

@section('content')
@include('admin.pendaftaran._header', [
    'jenis' => 'mahasiswa',
    'judul' => 'Pendaftaran Mahasiswa PPL',
    'deskripsi' => 'Verifikasi data formulir pendaftaran mahasiswa (Reguler & MBKM). Pendaftar yang diterima otomatis masuk ke Data Mahasiswa dan siap diplot ke kelompok.',
])

<div class="d-flex flex-wrap gap-2 mb-3 fs-7">
    <span class="badge bg-primary bg-opacity-10 text-primary border px-3 py-2">🏢 Reguler: {{ $stats['reguler'] }}</span>
    <span class="badge bg-info bg-opacity-10 text-info border px-3 py-2">🎓 MBKM: {{ $stats['mbkm'] }}</span>
</div>

<!-- Filter -->
<div class="card card-custom p-3 mb-3">
    <form action="{{ route('admin.pendaftaran.mahasiswa') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-12 col-md-4">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari Nama / NIM..." value="{{ request('search') }}">
        </div>
        <div class="col-4 col-md-2">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">-- Status --</option>
                @foreach(['menunggu' => 'Menunggu', 'diterima' => 'Diterima', 'ditolak' => 'Ditolak'] as $val => $label)
                    <option value="{{ $val }}" @selected(request('status') === $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-4 col-md-2">
            <select name="jenis_ppl" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">-- Jenis PPL --</option>
                <option value="reguler" @selected(request('jenis_ppl') === 'reguler')>Reguler</option>
                <option value="mbkm" @selected(request('jenis_ppl') === 'mbkm')>MBKM</option>
            </select>
        </div>
        <div class="col-4 col-md-2">
            <select name="prodi" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">-- Prodi --</option>
                @foreach(['Manajemen', 'Akuntansi', 'Bisnis Digital'] as $prodi)
                    <option value="{{ $prodi }}" @selected(request('prodi') === $prodi)>{{ $prodi }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-2 d-grid">
            <button type="submit" class="btn btn-sm btn-secondary fw-semibold">Filter</button>
        </div>
    </form>
</div>

<form id="bulkForm" action="{{ route('admin.pendaftaran.mahasiswa.bulk-terima') }}" method="POST" class="d-flex flex-wrap align-items-center gap-2 mb-3">
    @csrf
    <span class="text-muted fs-8">Aksi untuk pendaftar terpilih:</span>
    <button type="submit" class="btn btn-sm btn-outline-success fw-semibold" onclick="return confirm('Terima semua pendaftar terpilih yang masih menunggu? Pastikan bukti pembayaran sudah diperiksa.')">✓ Terima Semua Terpilih</button>
</form>

<div class="card card-custom overflow-hidden mb-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 fs-7">
            <thead class="bg-light text-secondary">
                <tr>
                    <th class="ps-3" style="width: 36px;"><input type="checkbox" class="form-check-input" id="checkAll"></th>
                    <th>Mahasiswa</th>
                    <th>Jenis PPL</th>
                    <th>Kontak</th>
                    <th>Bukti Bayar</th>
                    <th>Status</th>
                    <th class="text-end pe-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendaftaranList as $p)
                    <tr>
                        <td class="ps-3">
                            @if($p->status === 'menunggu')
                                <input type="checkbox" class="form-check-input row-check" name="ids[]" value="{{ $p->id }}" form="bulkForm">
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $p->nama }}</div>
                            <div class="text-muted fs-8"><code>{{ $p->nim }}</code> · {{ $p->prodi }}{{ $p->konsentrasi ? ' (' . $p->konsentrasi . ')' : '' }} · {{ $p->jenis_kelamin }}</div>
                            <div class="text-muted fs-8">Daftar: {{ $p->created_at->format('d/m/Y H:i') }} · MHS-{{ str_pad($p->id, 5, '0', STR_PAD_LEFT) }}</div>
                        </td>
                        <td>
                            @if($p->isMbkm())
                                <span class="badge bg-info text-dark">MBKM</span>
                                <div class="fs-8 text-muted mt-1">{{ $p->program_mbkm }}</div>
                                <div class="fs-8 text-muted">🏢 {{ $p->instansi_mbkm }}</div>
                            @else
                                <span class="badge bg-primary">Reguler</span>
                            @endif
                        </td>
                        <td class="fs-8">
                            <div>📱 {{ $p->no_hp }}</div>
                            @if($p->email)<div>✉️ {{ $p->email }}</div>@endif
                        </td>
                        <td>
                            <a href="{{ route('admin.pendaftaran.mahasiswa.file', $p) }}" target="_blank" class="btn btn-sm btn-outline-danger">📄 Lihat PDF</a>
                        </td>
                        <td>
                            @include('admin.pendaftaran._status')
                            @if($p->status === 'diterima' && $p->anggota)
                                <div class="fs-8 text-muted mt-1">{{ $p->anggota->kelompok->nama_kelompok ?? 'Belum diplot' }}</div>
                            @endif
                        </td>
                        <td class="text-end pe-3 text-nowrap">
                            @if($p->status === 'menunggu')
                                <form action="{{ route('admin.pendaftaran.mahasiswa.terima', $p) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">✓ Terima</button>
                                </form>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-tolak"
                                        data-action="{{ route('admin.pendaftaran.mahasiswa.tolak', $p) }}" data-nama="{{ $p->nama }} ({{ $p->nim }})">✗ Tolak</button>
                            @else
                                <span class="text-muted fs-8">{{ $p->diverifikasi_at?->format('d/m/Y') }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada data pendaftaran mahasiswa.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4 px-1">
    <div class="text-muted fs-7">Total <strong>{{ $pendaftaranList->total() }}</strong> pendaftar</div>
    <div>{{ $pendaftaranList->links() }}</div>
</div>
@endsection
