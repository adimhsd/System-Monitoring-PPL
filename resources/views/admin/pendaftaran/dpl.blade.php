@extends('layouts.app')

@section('title', 'Pendaftaran DPL')

@section('content')
@include('admin.pendaftaran._header', [
    'jenis' => 'dpl',
    'judul' => 'Pendaftaran Dosen Pembimbing Lapangan',
    'deskripsi' => 'Verifikasi formulir pendaftaran calon DPL beserta surat kesanggupan. Pendaftar yang diterima otomatis dibuatkan akun DPL.',
])

<div class="card card-custom p-3 mb-3">
    <form action="{{ route('admin.pendaftaran.dpl') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-12 col-md-6">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari Nama / NIP / NIDN..." value="{{ request('search') }}">
        </div>
        <div class="col-6 col-md-3">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">-- Status --</option>
                @foreach(['menunggu' => 'Menunggu', 'diterima' => 'Diterima', 'ditolak' => 'Ditolak'] as $val => $label)
                    <option value="{{ $val }}" @selected(request('status') === $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-3 d-grid">
            <button type="submit" class="btn btn-sm btn-secondary fw-semibold">Filter</button>
        </div>
    </form>
</div>

<div class="card card-custom overflow-hidden mb-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 fs-7">
            <thead class="bg-light text-secondary">
                <tr>
                    <th class="ps-3">Calon DPL</th>
                    <th>Kontak</th>
                    <th>Surat Kesanggupan</th>
                    <th>Status</th>
                    <th>Akun DPL</th>
                    <th class="text-end pe-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendaftaranList as $p)
                    <tr>
                        <td class="ps-3">
                            <div class="fw-bold text-dark">{{ $p->nama_lengkap }}</div>
                            <div class="text-muted fs-8">NIP/NIDN: <code>{{ $p->nip_nidn }}</code></div>
                            <div class="text-muted fs-8">Daftar: {{ $p->created_at->format('d/m/Y H:i') }} · DPL-{{ str_pad($p->id, 5, '0', STR_PAD_LEFT) }}</div>
                        </td>
                        <td class="fs-8">
                            <div>📱 {{ $p->no_hp }}</div>
                            <div>✉️ {{ $p->email }}</div>
                        </td>
                        <td>
                            <a href="{{ route('admin.pendaftaran.dpl.file', $p) }}" target="_blank" class="btn btn-sm btn-outline-danger">📄 Lihat PDF</a>
                        </td>
                        <td>@include('admin.pendaftaran._status')</td>
                        <td class="fs-8">
                            @if($p->user)
                                <strong>{{ $p->user->username }}</strong>
                                @if($p->user->must_change_password)
                                    <div class="text-muted">Password awal: {{ \App\Services\PendaftaranService::PASSWORD_DEFAULT_DPL }}</div>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-end pe-3 text-nowrap">
                            @if($p->status === 'menunggu')
                                <form action="{{ route('admin.pendaftaran.dpl.terima', $p) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Terima {{ addslashes($p->nama_lengkap) }} sebagai DPL dan buatkan akun?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">✓ Terima & Buat Akun</button>
                                </form>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-tolak"
                                        data-action="{{ route('admin.pendaftaran.dpl.tolak', $p) }}" data-nama="{{ $p->nama_lengkap }}">✗ Tolak</button>
                            @else
                                <span class="text-muted fs-8">{{ $p->diverifikasi_at?->format('d/m/Y') }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada data pendaftaran DPL.</td></tr>
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
