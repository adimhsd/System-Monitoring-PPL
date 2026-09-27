{{-- Tabel Standar Konversi Huruf Mutu FEB UNIKU (diadopsi dari SystemPenilaianPPL) --}}
@php
    $warnaHuruf = fn ($h) => match ($h) {
        'A', 'AB' => 'success',
        'B', 'BC' => 'info',
        'C', 'CD' => 'warning',
        default => 'danger',
    };
@endphp
<div class="card card-custom p-3 mb-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pb-2 mb-2 border-bottom">
        <span class="fw-bold fs-7 text-uppercase text-dark">
            <span class="d-inline-block rounded-circle bg-primary me-1" style="width:.5rem;height:.5rem;"></span>
            Standar Konversi Huruf Mutu
        </span>
        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 fs-8 fw-semibold px-2 py-1">
            Nilai Akhir = (Nilai Mitra × 60%) + (Nilai Laporan DPL × 40%)
        </span>
    </div>
    <div class="row row-cols-2 row-cols-sm-4 row-cols-lg-{{ min(count($skalaHuruf), 8) }} g-2">
        @foreach($skalaHuruf as $item)
            <div class="col">
                <div class="border rounded-3 text-center py-2 h-100 border-{{ $warnaHuruf($item['huruf']) }} border-opacity-50 bg-{{ $warnaHuruf($item['huruf']) }} bg-opacity-10">
                    <div class="fw-bold fs-5 text-{{ $warnaHuruf($item['huruf']) }}">{{ $item['huruf'] }}</div>
                    <div class="fs-8 text-muted">{{ rtrim(rtrim(number_format($item['min'], 2), '0'), '.') }} – {{ rtrim(rtrim(number_format($item['max'], 2), '0'), '.') }}</div>
                </div>
            </div>
        @endforeach
    </div>
</div>
