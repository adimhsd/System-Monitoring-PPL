@extends('layouts.app')

@section('title', 'Penilaian Mahasiswa — PIC Mitra')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Penilaian Mahasiswa PPL — PIC Mitra Lapangan</h4>
    <p class="text-muted mb-0 fs-7">Input skor penilaian 4 komponen (skala 0 - 100) untuk <strong>setiap mahasiswa</strong> bimbingan di instansi Anda (Bobot Mitra: <strong>60%</strong>).</p>
</div>

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
                            <th style="width: 20%;">Nama & NIM Mahasiswa</th>
                            <th style="width: 15%;">1. Kedisiplinan & Kehadiran</th>
                            <th style="width: 15%;">2. Etika & Sikap Kerja</th>
                            <th style="width: 15%;">3. Kerjasama Tim</th>
                            <th style="width: 15%;">4. Kualitas Hasil Kerja</th>
                            <th style="width: 10%;">Rata-rata (60%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kelompok->anggota as $mhs)
                            @php
                                $p = $mhs->penilaian;
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $mhs->nama }}</div>
                                    <div class="text-muted fs-8">NIM: <code>{{ $mhs->nim }}</code> ({{ $mhs->prodi }})</div>
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm text-center" name="nilai[{{ $mhs->id }}][kedisiplinan]" value="{{ old("nilai.{$mhs->id}.kedisiplinan", $p->mitra_skor_kedisiplinan ?? 85) }}" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm text-center" name="nilai[{{ $mhs->id }}][etika]" value="{{ old("nilai.{$mhs->id}.etika", $p->mitra_skor_etika ?? 85) }}" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm text-center" name="nilai[{{ $mhs->id }}][kerjasama]" value="{{ old("nilai.{$mhs->id}.kerjasama", $p->mitra_skor_kerjasama ?? 85) }}" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm text-center" name="nilai[{{ $mhs->id }}][hasil_kerja]" value="{{ old("nilai.{$mhs->id}.hasil_kerja", $p->mitra_skor_hasil_kerja ?? 85) }}" required>
                                </td>
                                <td class="text-center font-bold">
                                    <span class="fs-6 text-success fw-bold">{{ $p->total_nilai_mitra ?? '-' }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-success btn-touch text-white rounded-3 px-4 fw-semibold">
                    💾 Simpan Penilaian Mahasiswa {{ $kelompok->nama_kelompok }}
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
