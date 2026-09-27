{{-- Variabel: $jenis ('mahasiswa' | 'dpl'), $judul, $deskripsi, $dibuka, $stats --}}
<div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">{{ $judul }}</h4>
        <p class="text-muted mb-0 fs-7">{{ $deskripsi }}</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <div class="input-group input-group-sm" style="max-width: 360px;">
            <span class="input-group-text">🔗 Link</span>
            <input type="text" class="form-control" id="linkForm" value="{{ route('pendaftaran.' . $jenis) }}" readonly>
            <button class="btn btn-outline-secondary" type="button" onclick="navigator.clipboard.writeText(document.getElementById('linkForm').value); this.textContent='Tersalin ✓';">Salin</button>
        </div>
        <form action="{{ route('admin.pendaftaran.toggle', $jenis) }}" method="POST">
            @csrf
            <input type="hidden" name="dibuka" value="{{ $dibuka ? '0' : '1' }}">
            @if($dibuka)
                <button type="submit" class="btn btn-sm btn-outline-danger fw-semibold" onclick="return confirm('Tutup formulir pendaftaran? Pengunjung tidak dapat mengirim data baru.')">🔒 Tutup Pendaftaran</button>
            @else
                <button type="submit" class="btn btn-sm btn-success fw-semibold">🔓 Buka Pendaftaran</button>
            @endif
        </form>
    </div>
</div>

<div class="alert {{ $dibuka ? 'alert-success' : 'alert-secondary' }} py-2 fs-7 mb-3">
    Status formulir publik: <strong>{{ $dibuka ? 'DIBUKA' : 'DITUTUP' }}</strong>
    — bagikan link di atas kepada {{ $jenis === 'dpl' ? 'calon DPL' : 'mahasiswa' }} (tanpa perlu login).
</div>

<div class="row g-3 mb-4">
    @foreach([
        ['Total Pendaftar', $stats['total'], 'primary'],
        ['Menunggu Verifikasi', $stats['menunggu'], 'warning'],
        ['Diterima', $stats['diterima'], 'success'],
        ['Ditolak', $stats['ditolak'], 'danger'],
    ] as [$label, $nilai, $warna])
        <div class="col-6 col-xl-3">
            <div class="card card-custom p-3 border-start border-4 border-{{ $warna }} h-100">
                <span class="text-{{ $warna === 'warning' ? 'dark' : $warna }} fs-7 fw-bold">{{ $label }}</span>
                <h3 class="fw-bold text-dark mb-0 mt-1">{{ $nilai }}</h3>
            </div>
        </div>
    @endforeach
</div>

@if($errors->any())
    <div class="alert alert-danger fs-7">{{ $errors->first() }}</div>
@endif

<!-- Modal Tolak Pendaftaran -->
<div class="modal fade" id="modalTolak" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card-custom">
            <form id="formTolak" method="POST">
                @csrf
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold">Tolak Pendaftaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body fs-7">
                    <p class="mb-2">Tolak pendaftaran <strong id="namaTolak"></strong>?</p>
                    <label for="catatan_admin" class="form-label fw-semibold">Alasan penolakan (opsional)</label>
                    <textarea class="form-control" id="catatan_admin" name="catatan_admin" rows="3" maxlength="500" placeholder="Contoh: Bukti pembayaran tidak terbaca / nominal tidak sesuai."></textarea>
                    <div class="form-text">Pendaftar yang ditolak dapat mengirim ulang formulir dengan {{ $jenis === 'dpl' ? 'NIP/NIDN' : 'NIM' }} yang sama.</div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3">Tolak Pendaftaran</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.btn-tolak').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.getElementById('formTolak').action = btn.dataset.action;
                document.getElementById('namaTolak').textContent = btn.dataset.nama;
                bootstrap.Modal.getOrCreateInstance(document.getElementById('modalTolak')).show();
            });
        });
        const checkAll = document.getElementById('checkAll');
        if (checkAll) {
            checkAll.addEventListener('change', () => document.querySelectorAll('.row-check').forEach(cb => cb.checked = checkAll.checked));
        }
    });
</script>
@endpush
