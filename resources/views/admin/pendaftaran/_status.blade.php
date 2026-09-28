@if($p->status === 'diterima')
    <span class="badge bg-success">✓ Diterima</span>
@elseif($p->status === 'ditolak')
    <span class="badge bg-danger">✗ Ditolak</span>
    @if($p->catatan_admin)
        <div class="text-muted fs-8 mt-1" style="max-width: 200px;">{{ $p->catatan_admin }}</div>
    @endif
@else
    <span class="badge bg-warning text-dark">⏳ Menunggu</span>
@endif
