@extends('layouts.app')

@section('title', 'Buku Panduan & Pedoman PPL')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">📖 Buku Panduan & Pedoman Resmi PPL FEB UNIKU</h4>
        <p class="text-muted mb-0 fs-7">Petunjuk teknis pelaksanaan Praktik Pengenalan Lapangan (PPL) bagi Mahasiswa, DPL, dan Mitra Penempatan.</p>
    </div>
    <div>
        <a href="{{ $driveViewUrl }}" target="_blank" class="btn btn-outline-primary btn-touch rounded-3 fw-semibold">
            🔗 Buka di Google Drive
        </a>
    </div>
</div>

<div class="card card-custom p-2 overflow-hidden" style="min-height: 750px;">
    <iframe 
        src="{{ $driveEmbedUrl }}" 
        width="100%" 
        height="750px" 
        style="border: none; border-radius: 10px;" 
        allow="autoplay">
    </iframe>
</div>
@endsection
