@extends('layouts.app')

@section('title', 'Pemantauan Logbook PIC Mitra')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Pemantauan Logbook Mahasiswa Magang</h4>
    <p class="text-muted mb-0 fs-7">Tinjau dan approve logbook kegiatan harian mahasiswa magang PPL di instansi Anda.</p>
</div>

@if(!$kelompok)
    <div class="card card-custom p-4 text-center text-muted">
        Belum ada kelompok magang PPL yang ditempatkan di instansi Anda.
    </div>
@else
    <div class="card card-custom p-3 mb-4">
        <form action="{{ route('pic.logbook.index') }}" method="GET" class="row g-2">
            <div class="col-12 col-md-8">
                <select name="status_dilihat" class="form-select fs-7">
                    <option value="">-- Semua Status Approval --</option>
                    <option value="belum" {{ request('status_dilihat') == 'belum' ? 'selected' : '' }}>Belum Di-Approve Pembimbing Mitra</option>
                    <option value="sudah" {{ request('status_dilihat') == 'sudah' ? 'selected' : '' }}>Sudah Di-Approve Pembimbing Mitra</option>
                </select>
            </div>
            <div class="col-12 col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary fs-7 w-100">Filter</button>
                <a href="{{ route('pic.logbook.index') }}" class="btn btn-outline-secondary fs-7">Reset</a>
            </div>
        </form>
    </div>

    <div class="card card-custom overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 fs-7">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="ps-4">Tanggal</th>
                        <th>Waktu Magang</th>
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
                                🕒 {{ \Carbon\Carbon::parse($logbook->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($logbook->waktu_selesai)->format('H:i') }}
                            </td>
                            <td class="text-truncate" style="max-width: 300px;">
                                {{ $logbook->deskripsi_kegiatan }}
                            </td>
                            <td>
                                @if($logbook->dilihat_mitra)
                                    @if(($logbook->status_validasi_mitra ?? 'sesuai') === 'tidak_sesuai')
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">
                                            🔴 Tidak Sesuai
                                        </span>
                                    @else
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                            🟢 Sesuai
                                        </span>
                                    @endif
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-dark border border-warning">
                                        Belum Di-Approve PIC
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('pic.logbook.show', $logbook) }}" class="btn btn-sm btn-outline-primary">
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
@endif
@endsection
