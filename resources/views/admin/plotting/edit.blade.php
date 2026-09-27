@extends('layouts.app')

@section('title', 'Form Plotting Kelompok PPL')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.plotting.index') }}" class="text-decoration-none text-secondary fs-7">&larr; Kembali ke Plotting Kelompok</a>
    <h4 class="fw-bold mb-1 mt-1">Form Plotting & Pemetaan — {{ $kelompok->nama_kelompok }}</h4>
    <p class="text-muted mb-0 fs-7">Petakan Mitra Instansi, DPL Pembimbing (maksimal 10 mahasiswa bimbingan), dan daftarkan anggota mahasiswa (1-10 mahasiswa).</p>
</div>

<div class="row g-4 max-w-3xl mx-auto">
    <div class="col-12">
        <div class="card card-custom p-4">
            <form action="{{ route('admin.plotting.update', $kelompok) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- 1. Mitra Penempatan -->
                <div class="mb-4 p-3 bg-light rounded-3 border">
                    <label for="mitra_id" class="form-label fw-bold text-dark fs-6 mb-1">1. Pilih Mitra Instansi Penempatan <span class="text-danger">*</span></label>
                    <p class="text-muted fs-8 mb-2">Pilih lokasi instansi atau SKPD tempat kelompok ini melaksanakan PPL.</p>
                    <select class="form-select @error('mitra_id') is-invalid @enderror" id="mitra_id" name="mitra_id" required>
                        <option value="">-- Pilih Mitra Penempatan --</option>
                        @foreach($mitraList as $m)
                            <option value="{{ $m->id }}" {{ old('mitra_id', $kelompok->mitra_id) == $m->id ? 'selected' : '' }}>
                                🏢 {{ $m->nama_mitra }} (Kategori: {{ $m->kategori }})
                            </option>
                        @endforeach
                    </select>
                    @error('mitra_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 2. DPL Pembimbing -->
                <div class="mb-4 p-3 bg-light rounded-3 border">
                    <label for="dpl_id" class="form-label fw-bold text-dark fs-6 mb-1">2. Pilih Dosen Pembimbing Lapangan (DPL) <span class="text-danger">*</span></label>
                    <p class="text-muted fs-8 mb-2">Beban bimbingan DPL dibatasi maksimal <strong>30 mahasiswa</strong>.</p>
                    <select class="form-select @error('dpl_id') is-invalid @enderror" id="dpl_id" name="dpl_id" required>
                        <option value="">-- Pilih Dosen Pembimbing --</option>
                        @foreach($dplList as $d)
                            @php
                                $load = $d->total_bimbingan_mhs ?? 0;
                                if ($load >= 30) {
                                    $statusBadge = '🔴 (' . $load . '/30 Mhs - Penuh)';
                                } elseif ($load > 20) {
                                    $statusBadge = '🔥 (' . $load . '/30 Mhs - Beban Tinggi)';
                                } elseif ($load > 10) {
                                    $statusBadge = '⚠️ (' . $load . '/30 Mhs - Beban >10)';
                                } else {
                                    $statusBadge = '🟢 (' . $load . '/30 Mhs)';
                                }
                            @endphp
                            <option value="{{ $d->id }}" {{ old('dpl_id', $kelompok->dpl_id) == $d->id ? 'selected' : '' }}>
                                👨‍🏫 {{ $d->nama_lengkap }} — Beban: {{ $load }}/30 Mahasiswa {{ $statusBadge }}
                            </option>
                        @endforeach
                    </select>
                    @error('dpl_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 3. Anggota Mahasiswa -->
                <div class="mb-4 p-3 bg-light rounded-3 border" x-data="{ searchMhs: '' }">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label fw-bold text-dark fs-6 mb-0">3. Pilih Mahasiswa Anggota Kelompok <span class="text-danger">*</span></label>
                        <span class="badge bg-secondary">Minimal 1, Maksimal 20 Mahasiswa</span>
                    </div>
                    <p class="text-muted fs-8 mb-2">Centang mahasiswa yang akan dimasukkan ke dalam {{ $kelompok->nama_kelompok }}:</p>

                    <!-- Input Live Search Filter Mahasiswa -->
                    <div class="mb-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white text-muted">🔍</span>
                            <input type="text" 
                                   x-model="searchMhs" 
                                   class="form-control form-control-sm bg-white" 
                                   placeholder="Cari berdasarkan Nama Mahasiswa, NIM, Prodi, atau Konsentrasi..." 
                                   id="searchStudentInput">
                            <button type="button" class="btn btn-outline-secondary" x-show="searchMhs" @click="searchMhs = ''">&times; Reset</button>
                        </div>
                    </div>

                    @error('mahasiswa_ids')
                        <div class="alert alert-danger fs-7 py-2 mb-3">{{ $message }}</div>
                    @enderror

                    <div class="table-responsive border rounded-3 bg-white" style="max-height: 480px; overflow-y: auto;">
                        <table class="table table-hover table-sm align-middle mb-0 fs-7">
                            <thead class="bg-light sticky-top" style="z-index: 5;">
                                <tr>
                                    <th style="width: 40px;" class="text-center">#</th>
                                    <th>NIM</th>
                                    <th>Nama Mahasiswa</th>
                                    <th>Gender</th>
                                    <th>Program Studi</th>
                                    <th>Konsentrasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $selectedIds = old('mahasiswa_ids', $kelompok->anggota->pluck('id')->toArray());
                                @endphp
                                @forelse($availableMahasiswa as $mhs)
                                    <tr x-show="!searchMhs || '{{ strtolower($mhs->nama . ' ' . $mhs->nim . ' ' . $mhs->prodi . ' ' . ($mhs->konsentrasi ?? '')) }}'.includes(searchMhs.toLowerCase())">
                                        <td class="text-center">
                                            <input type="checkbox" name="mahasiswa_ids[]" value="{{ $mhs->id }}" class="form-check-input" {{ in_array($mhs->id, $selectedIds) ? 'checked' : '' }}>
                                        </td>
                                        <td><code>{{ $mhs->nim }}</code></td>
                                        <td class="fw-bold text-dark">{{ $mhs->nama }}</td>
                                        <td>
                                            <span class="fs-8 text-secondary">{{ $mhs->jenis_kelamin }}</span>
                                        </td>
                                        <td><span class="badge bg-light text-dark border">{{ $mhs->prodi }}</span></td>
                                        <td>{{ $mhs->konsentrasi ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-3 text-muted">Tidak ada mahasiswa yang tersedia untuk diplotkan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <button type="submit" class="btn btn-success btn-touch text-white rounded-3 w-100 fw-bold">
                    💾 Simpan & Perbarui Plotting {{ $kelompok->nama_kelompok }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
