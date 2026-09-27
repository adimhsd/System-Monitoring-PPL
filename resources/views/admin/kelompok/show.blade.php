@extends('layouts.app')

@section('title', 'Detail Kelompok PPL')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <a href="{{ route('admin.kelompok.index') }}" class="text-decoration-none text-secondary fs-7">&larr; Kembali ke Daftar Kelompok</a>
        <h4 class="fw-bold mb-1 mt-1">{{ $kelompok->nama_kelompok }}</h4>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.kelompok.edit', $kelompok) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
            ✏️ Reassign / Edit
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <!-- Card Mitra -->
    <div class="col-12 col-md-4">
        <div class="card card-custom p-3 h-100 border-start border-4 border-primary">
            <h6 class="fw-bold text-muted fs-7 mb-2 text-uppercase">Mitra Penempatan PPL</h6>
            <h5 class="fw-bold text-dark mb-1">{{ $kelompok->mitra->nama_mitra }}</h5>
            <p class="text-secondary fs-7 mb-2">Kategori: <strong>{{ $kelompok->mitra->kategori }}</strong></p>
            <hr class="my-2">
            <p class="text-secondary fs-7 mb-0">
                👤 <strong>PIC Mitra:</strong> {{ $kelompok->mitra->picUser->nama_lengkap ?? 'Belum ada PIC' }}
                @if($kelompok->mitra->picUser)
                    <br><span class="text-muted fs-8">Hp: {{ $kelompok->mitra->picUser->no_hp ?? '-' }}</span>
                @endif
            </p>
        </div>
    </div>

    <!-- Card DPL -->
    <div class="col-12 col-md-4">
        <div class="card card-custom p-3 h-100 border-start border-4 border-info">
            <h6 class="fw-bold text-muted fs-7 mb-2 text-uppercase">Dosen Pembimbing (DPL)</h6>
            <h5 class="fw-bold text-dark mb-1">{{ $kelompok->dpl->nama_lengkap }}</h5>
            <p class="text-secondary fs-7 mb-2">NIP/NIDN: {{ $kelompok->dpl->nip_nidn ?? '-' }}</p>
            <hr class="my-2">
            <p class="text-secondary fs-7 mb-0">
                📞 <strong>No HP:</strong> {{ $kelompok->dpl->no_hp ?? '-' }}
            </p>
        </div>
    </div>

    <!-- Card Akun Login -->
    <div class="col-12 col-md-4">
        <div class="card card-custom p-3 h-100 border-start border-4 border-warning">
            <h6 class="fw-bold text-muted fs-7 mb-2 text-uppercase">Akun Login Kelompok PPL</h6>
            <h5 class="fw-bold text-dark mb-1">🔑 {{ $kelompok->nama_kelompok }}</h5>
            <p class="text-secondary fs-7 mb-2">Username: <strong>{{ $kelompok->ketua->username }}</strong></p>
        </div>
    </div>
</div>

<div class="card card-custom p-4">
    @php $mhsCount = $kelompok->anggota->count(); @endphp
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Daftar Anggota Kelompok</h5>
        @if($mhsCount > 15)
            <span class="badge bg-danger px-3 py-1">🔥 {{ $mhsCount }} / 20 Mahasiswa (Kelompok Besar)</span>
        @elseif($mhsCount > 10)
            <span class="badge bg-warning text-dark px-3 py-1">⚠️ {{ $mhsCount }} / 20 Mahasiswa (Kelompok Besar >10)</span>
        @else
            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-3 py-1">🟢 {{ $mhsCount }} / 20 Mahasiswa</span>
        @endif
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 fs-7">
            <thead class="bg-light text-secondary">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>NIM</th>
                    <th>Nama Mahasiswa</th>
                    <th>Jenis Kelamin</th>
                    <th>Program Studi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kelompok->anggota as $index => $mhs)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="fw-semibold text-dark">{{ $mhs->nim }}</td>
                        <td>
                            {{ $mhs->nama }}
                            @if($mhs->nama === $kelompok->ketua->nama_lengkap)
                                <span class="badge bg-warning text-dark ms-1">Ketua</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $mhs->jenis_kelamin ?? 'Laki-laki' }}</span>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $mhs->prodi }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
