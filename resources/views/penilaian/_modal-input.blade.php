{{--
    Modal Input Nilai dengan Live Preview Kalkulasi (diadopsi dari SystemPenilaianPPL).
    Variabel: $role ('admin' | 'dpl'), $skalaHuruf
    - Admin : dapat mengisi Nilai Mitra & Nilai Laporan DPL.
    - DPL   : mengisi Nilai Laporan DPL; Nilai Mitra berasal dari PIC Mitra (hanya tampil),
              kecuali kelompok MBKM di mana DPL juga mengisi Nilai Mitra.
--}}
@php $isAdmin = $role === 'admin'; @endphp

<div class="modal fade" id="modalInputNilai" tabindex="-1" aria-labelledby="modalInputNilaiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card-custom">
            <form id="formInputNilai" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header border-bottom">
                    <div>
                        <h5 class="modal-title fw-bold" id="modalInputNilaiLabel">Input Nilai</h5>
                        <div class="text-muted fs-8" id="modalInputNilaiDesc"></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body fs-7">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="inputNilaiMitra">Nilai Mitra / Lapangan (Bobot 60%)</label>
                        <input type="number" step="0.01" min="0" max="100" class="form-control" id="inputNilaiMitra" name="nilai_mitra" placeholder="0 - 100">
                        <textarea class="form-control form-control-sm mt-2" id="inputCatatanMitra" name="catatan_mitra" rows="1" placeholder="Catatan Mitra (opsional)"></textarea>
                        @unless($isAdmin)
                            <div class="form-text" id="helpNilaiMitra"></div>
                        @endunless
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="inputNilaiDpl">Nilai Laporan DPL (Bobot 40%)</label>
                        <input type="number" step="0.01" min="0" max="100" class="form-control" id="inputNilaiDpl" name="nilai_dpl" placeholder="0 - 100" {{ $isAdmin ? '' : 'required' }}>
                        <textarea class="form-control form-control-sm mt-2" id="inputCatatanDpl" name="catatan_dpl" rows="1" placeholder="Catatan DPL (opsional)"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Live Preview Kalkulasi</label>
                        <div class="form-control bg-light fw-semibold" id="previewKalkulasi">-</div>
                        <div class="form-text">
                            Standar Huruf Mutu:
                            @foreach($skalaHuruf as $item)
                                {{ $item['huruf'] }} ({{ rtrim(rtrim(number_format($item['min'], 2), '0'), '.') }}–{{ rtrim(rtrim(number_format($item['max'], 2), '0'), '.') }}){{ $loop->last ? '' : ',' }}
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="form-label fw-semibold" for="inputStatus">Status Simpan</label>
                        <select class="form-select" id="inputStatus" name="status" required>
                            <option value="draft">Simpan sebagai Draft (Masih bisa diedit)</option>
                            <option value="locked">Kunci Nilai (Final)</option>
                        </select>
                        <div class="form-text">Nilai hanya dapat dikunci jika Nilai Mitra & Nilai Laporan DPL sudah lengkap. Nilai terkunci hanya dapat dibuka oleh Admin.</div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">💾 Simpan Nilai</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const skala = @json($skalaHuruf);
        const isAdmin = @json($isAdmin);
        const modalEl = document.getElementById('modalInputNilai');
        const form = document.getElementById('formInputNilai');
        const mitra = document.getElementById('inputNilaiMitra');
        const dpl = document.getElementById('inputNilaiDpl');
        const catatanMitra = document.getElementById('inputCatatanMitra');
        const catatanDpl = document.getElementById('inputCatatanDpl');
        const status = document.getElementById('inputStatus');
        const preview = document.getElementById('previewKalkulasi');

        function huruf(nilai) {
            const item = skala.find(s => nilai >= parseFloat(s.min) && nilai <= parseFloat(s.max));
            return item ? item.huruf : 'E';
        }

        function hitung() {
            const m = parseFloat(mitra.value);
            const d = parseFloat(dpl.value);
            const lengkap = !isNaN(m) && !isNaN(d);

            status.querySelector('option[value="locked"]').disabled = !lengkap;
            if (!lengkap && status.value === 'locked') status.value = 'draft';

            if (!lengkap) {
                preview.textContent = isNaN(m)
                    ? 'Menunggu Nilai Mitra — Nilai Akhir terbentuk setelah kedua nilai lengkap.'
                    : 'Masukkan Nilai Laporan DPL untuk melihat kalkulasi otomatis.';
                return;
            }

            const akhir = Math.round(((m * 0.60) + (d * 0.40)) * 100) / 100;
            preview.textContent = 'Nilai Akhir: ' + akhir.toFixed(2) + ' | Grade: ' + huruf(akhir);
        }

        document.querySelectorAll('.btn-input-nilai').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const ds = btn.dataset;
                form.action = ds.action;
                document.getElementById('modalInputNilaiLabel').textContent = 'Input Nilai: ' + ds.nama + ' (' + ds.nim + ')';
                document.getElementById('modalInputNilaiDesc').textContent = 'Kelompok: ' + ds.kelompok + ' | Mitra: ' + ds.mitra;
                mitra.value = ds.nilaiMitra || '';

                // DPL hanya mengisi Nilai Mitra untuk kelompok MBKM
                if (!isAdmin) {
                    const mbkm = ds.mbkm === '1';
                    mitra.readOnly = !mbkm;
                    mitra.required = mbkm;
                    mitra.classList.toggle('bg-light', !mbkm);
                    if (mbkm) {
                        mitra.setAttribute('name', 'nilai_mitra');
                        catatanMitra.setAttribute('name', 'catatan_mitra');
                    } else {
                        mitra.removeAttribute('name');
                        catatanMitra.removeAttribute('name');
                    }
                    catatanMitra.classList.toggle('d-none', !mbkm);
                    document.getElementById('helpNilaiMitra').textContent = mbkm
                        ? 'Kelompok MBKM: Nilai Mitra diinput langsung oleh DPL.'
                        : 'Nilai Mitra diinput oleh PIC Mitra melalui akunnya.';
                }
                dpl.value = ds.nilaiDpl || '';
                catatanMitra.value = ds.catatanMitra || '';
                catatanDpl.value = ds.catatanDpl || '';
                status.value = ds.status || 'draft';
                hitung();
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            });
        });

        mitra.addEventListener('input', hitung);
        dpl.addEventListener('input', hitung);

        // Pilih semua checkbox untuk aksi massal
        const checkAll = document.getElementById('checkAll');
        if (checkAll) {
            checkAll.addEventListener('change', function () {
                document.querySelectorAll('.row-check').forEach(cb => cb.checked = checkAll.checked);
            });
        }
    });
</script>
@endpush
