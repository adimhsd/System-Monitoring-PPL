@extends('layouts.app')

@section('title', 'Logbook Kegiatan Harian')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Logbook Kegiatan Harian PPL</h4>
        <p class="text-muted mb-0 fs-7">Pelaporan aktivitas magang harian untuk kelompok: <strong>{{ $kelompok->nama_kelompok }}</strong></p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('ketua.logbook.pdf') }}" class="btn btn-outline-danger btn-touch rounded-3 fw-semibold fs-7">
            📄 Cetak Laporan PDF
        </a>
        <a href="{{ route('ketua.logbook.create') }}" class="btn btn-primary btn-touch text-white rounded-3 fw-semibold">
            + Input Logbook Hari Ini
        </a>
    </div>
</div>

@if($logbookList->isEmpty())
    <div class="card card-custom p-4 text-center text-muted">
        Belum ada entri logbook harian yang dibuat. Klik tombol di atas untuk mengisi logbook pertama Anda.
    </div>
@else
    <div class="row g-3">
        @foreach($logbookList as $logbook)
            <div class="col-12">
                <div class="card card-custom p-3 border-start border-4 {{ $logbook->terlambat ? 'border-warning' : 'border-primary' }}">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="fw-bold text-dark mb-0">{{ $logbook->tanggal->translatedFormat('l, d F Y') }}</h6>
                            <span class="badge bg-light text-secondary border">
                                🕒 {{ \Carbon\Carbon::parse($logbook->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($logbook->waktu_selesai)->format('H:i') }} WIB
                            </span>
                            @if($logbook->terlambat)
                                <span class="badge bg-warning text-dark">⚠️ Terlambat</span>
                            @endif
                        </div>

                        <!-- Status Badge Approval Mitra & DPL -->
                        <div class="d-flex gap-2">
                            @if($logbook->dilihat_mitra)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success fs-8" title="Di-approve pada {{ $logbook->dilihat_mitra_at?->format('d/m/Y H:i') }}">
                                    ✓ Approved PIC Mitra
                                </span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border fs-8">
                                    Belum Di-Approve Mitra
                                </span>
                            @endif

                            @if($logbook->dilihat_dpl)
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary fs-8" title="Di-approve pada {{ $logbook->dilihat_dpl_at?->format('d/m/Y H:i') }}">
                                    ✓ Approved DPL
                                </span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border fs-8">
                                    Belum Di-Approve DPL
                                </span>
                            @endif
                        </div>
                    </div>

                    <p class="text-secondary fs-7 mb-3 text-break">
                        {{ $logbook->deskripsi_kegiatan }}
                    </p>

                    <div class="d-flex justify-content-between align-items-center border-top pt-2">
                        <a href="{{ route('foto.show', $logbook) }}" target="_blank" class="btn btn-sm btn-outline-secondary fs-8">
                            📷 Lihat Foto Dokumentasi
                        </a>
                        <a href="{{ route('ketua.logbook.edit', $logbook) }}" class="btn btn-sm btn-outline-primary fs-8">
                            ✏️ Edit Logbook
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-3">
        {{ $logbookList->links() }}
    </div>
@endif
@endsection
