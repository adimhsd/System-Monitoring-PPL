{{--
    Tabel Input & Rekap Nilai (Admin / DPL).
    Variabel: $mahasiswaList (paginator), $role ('admin' | 'dpl')
--}}
@php
    $isAdmin = $role === 'admin';
    $warnaHuruf = fn ($h) => match ($h) {
        'A', 'AB' => 'success',
        'B', 'BC' => 'info',
        'C', 'CD' => 'warning',
        'D', 'E' => 'danger',
        default => 'secondary',
    };
    $warnaProdi = fn ($prodi) => match ($prodi) {
        'Manajemen' => 'info',
        'Akuntansi' => 'success',
        'Bisnis Digital' => 'warning',
        default => 'secondary',
    };
    $fmt = fn ($n, $d = 2) => $n !== null ? number_format((float) $n, $d) : null;
@endphp

<div class="card card-custom overflow-hidden mb-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 fs-7">
            <thead class="bg-light text-secondary">
                <tr>
                    <th class="ps-3" style="width: 36px;">
                        <input type="checkbox" class="form-check-input" id="checkAll" title="Pilih semua di halaman ini">
                    </th>
                    <th>NIM & Nama Mahasiswa</th>
                    <th>Kelompok & Mitra</th>
                    @if($isAdmin)
                        <th>DPL</th>
                    @endif
                    <th class="text-center">Mitra (60%)</th>
                    <th class="text-center">Laporan (40%)</th>
                    <th class="text-center">Nilai Akhir</th>
                    <th class="text-center">Grade</th>
                    <th class="text-center">Status</th>
                    <th class="text-end pe-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mahasiswaList as $mhs)
                    @php
                        $p = $mhs->penilaian;
                        $locked = (bool) $p?->isLocked();
                        $bolehInput = ! $locked || $isAdmin;
                    @endphp
                    <tr class="{{ $locked ? 'table-success bg-opacity-25' : '' }}">
                        <td class="ps-3">
                            <input type="checkbox" class="form-check-input row-check" name="ids[]" value="{{ $mhs->id }}" form="bulkForm">
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $mhs->nama }}</div>
                            <div class="text-muted fs-8">
                                <code>{{ $mhs->nim }}</code>
                                <span class="badge bg-{{ $warnaProdi($mhs->prodi) }} bg-opacity-10 text-{{ $warnaProdi($mhs->prodi) }} border border-{{ $warnaProdi($mhs->prodi) }} border-opacity-25 ms-1">{{ $mhs->prodi }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $mhs->kelompok->nama_kelompok ?? '-' }}</span>
                            <div class="text-muted fs-8 text-truncate" style="max-width: 220px;" title="{{ $mhs->kelompok->mitra->nama_mitra ?? '-' }}">🏢 {{ $mhs->kelompok->mitra->nama_mitra ?? '-' }}</div>
                        </td>
                        @if($isAdmin)
                            <td class="fs-8">{{ $mhs->kelompok->dpl->nama_lengkap ?? '-' }}</td>
                        @endif
                        <td class="text-center">
                            @if($p?->nilai_mitra !== null)
                                <span class="fw-semibold text-success">{{ $fmt($p->nilai_mitra, 1) }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($p?->nilai_dpl !== null)
                                <span class="fw-semibold text-primary">{{ $fmt($p->nilai_dpl, 1) }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center fw-bold">{{ $fmt($p?->nilai_akhir) ?? '-' }}</td>
                        <td class="text-center">
                            @if($p?->nilai_huruf)
                                <span class="badge bg-{{ $warnaHuruf($p->nilai_huruf) }} px-2 py-1 fs-7">{{ $p->nilai_huruf }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($locked)
                                <span class="badge bg-success">🔒 Final (Terkunci)</span>
                            @elseif($p?->nilai_akhir !== null)
                                <span class="badge bg-warning text-dark">Draft</span>
                            @else
                                <span class="badge bg-light text-muted border">Belum Lengkap</span>
                            @endif
                        </td>
                        <td class="text-end pe-3 text-nowrap">
                            @if($bolehInput)
                                <button type="button"
                                        class="btn btn-sm btn-primary btn-input-nilai"
                                        data-action="{{ route($role . '.penilaian.update', $mhs) }}"
                                        data-nama="{{ $mhs->nama }}"
                                        data-nim="{{ $mhs->nim }}"
                                        data-kelompok="{{ $mhs->kelompok->nama_kelompok ?? '-' }}"
                                        data-mitra="{{ $mhs->kelompok->mitra->nama_mitra ?? '-' }}"
                                        data-nilai-mitra="{{ $p?->nilai_mitra }}"
                                        data-nilai-dpl="{{ $p?->nilai_dpl }}"
                                        data-catatan-mitra="{{ $p?->catatan_mitra }}"
                                        data-catatan-dpl="{{ $p?->catatan_dpl }}"
                                        data-status="{{ $p?->status ?? 'draft' }}">
                                    ✏️ Input Nilai
                                </button>
                            @endif

                            @if(! $locked && $p?->nilai_akhir !== null)
                                <form action="{{ route($role . '.penilaian.lock', $mhs) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Kunci nilai {{ addslashes($mhs->nama) }}? Nilai yang sudah dikunci tidak dapat diubah oleh DPL / PIC Mitra kecuali dibuka oleh Admin.')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success">🔒 Kunci</button>
                                </form>
                            @endif

                            @if($locked && $isAdmin)
                                <form action="{{ route('admin.penilaian.unlock', $mhs) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Buka kunci nilai {{ addslashes($mhs->nama) }}? DPL & PIC Mitra akan dapat mengubah kembali nilai ini.')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-warning">🔓 Buka Kunci</button>
                                </form>
                            @endif

                            @if($locked && ! $isAdmin)
                                <span class="text-muted fs-8">Hubungi Admin untuk revisi</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $isAdmin ? 10 : 9 }}" class="text-center py-4 text-muted">Tidak ada data mahasiswa yang sesuai filter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2 mb-4 px-1">
    <div class="text-muted fs-7">
        Menampilkan <strong>{{ $mahasiswaList->firstItem() ?? 0 }}</strong> – <strong>{{ $mahasiswaList->lastItem() ?? 0 }}</strong> dari <strong>{{ $mahasiswaList->total() }}</strong> Mahasiswa
    </div>
    <div>
        {{ $mahasiswaList->links() }}
    </div>
</div>
