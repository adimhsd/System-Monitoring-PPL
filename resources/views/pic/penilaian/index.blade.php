@extends('layouts.app')

@section('title', 'Penilaian Mahasiswa — PIC Mitra')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Penilaian Mahasiswa PPL — PIC Mitra Lapangan</h4>
    <p class="text-muted mb-0 fs-7">Input <strong>Nilai Mitra / Lapangan</strong> (skala 0 - 100) untuk setiap mahasiswa di instansi Anda. Bobot Nilai Mitra: <strong>60%</strong> dari Nilai Akhir PPL.</p>
</div>

@include('penilaian._errors')
@include('penilaian._standar-huruf')

@forelse($kelompokList as $kelompok)
    <div class="card card-custom p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <div>
                <h5 class="fw-bold text-primary mb-1">🏢 {{ $kelompok->nama_kelompok }}</h5>
                <p class="text-muted fs-8 mb-0">DPL Pembimbing: {{ $kelompok->dpl->nama_lengkap ?? '-' }} | Ketua: {{ $kelompok->ketua->nama_lengkap ?? '-' }}</p>
            </div>
            <span class="badge bg-success px-3 py-2 fs-7">{{ $kelompok->anggota->count() }} Mahasiswa</span>
        </div>

        <form action="{{ route('pic.penilaian.store', $kelompok) }}" method="POST">
            @csrf

            <div class="table-responsive mb-3">
                <table class="table table-bordered align-middle mb-0 fs-7">
                    <thead class="bg-light text-secondary text-center">
                        <tr>
                            <th style="width: 32%;">Nama & NIM Mahasiswa</th>
                            <th style="width: 18%;">Nilai Mitra (0 - 100)</th>
                            <th>Catatan Mitra (opsional)</th>
                            <th style="width: 14%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kelompok->anggota as $mhs)
                            @php
                                $p = $mhs->penilaian;
                                $locked = (bool) $p?->isLocked();
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $mhs->nama }}</div>
                                    <div class="text-muted fs-8">NIM: <code>{{ $mhs->nim }}</code> ({{ $mhs->prodi }})</div>
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" max="100"
                                           class="form-control form-control-sm text-center fw-semibold"
                                           name="nilai[{{ $mhs->id }}][nilai_mitra]"
                                           value="{{ old("nilai.{$mhs->id}.nilai_mitra", $p?->nilai_mitra) }}"
                                           placeholder="0 - 100"
                                           @disabled($locked)>
                                </td>
                                <td>
                                    <input type="text" maxlength="1000"
                                           class="form-control form-control-sm"
                                           name="nilai[{{ $mhs->id }}][catatan_mitra]"
                                           value="{{ old("nilai.{$mhs->id}.catatan_mitra", $p?->catatan_mitra) }}"
                                           @disabled($locked)>
                                </td>
                                <td class="text-center">
                                    @if($locked)
                                        <span class="badge bg-success">🔒 Final</span>
                                    @elseif($p?->nilai_mitra !== null)
                                        <span class="badge bg-info">Sudah Diisi</span>
                                    @else
                                        <span class="badge bg-light text-muted border">Belum Diisi</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                <span class="text-muted fs-8">Nilai yang sudah dikunci (Final) oleh DPL/Admin tidak dapat diubah.</span>
                <button type="submit" class="btn btn-success btn-touch text-white rounded-3 px-4 fw-semibold">
                    💾 Simpan Nilai Mitra {{ $kelompok->nama_kelompok }}
                </button>
            </div>
        </form>
    </div>
@empty
    <div class="card card-custom p-4 text-center text-muted">
        Belum ada kelompok PPL yang ditempatkan di instansi Anda.
    </div>
@endforelse
@endsection
