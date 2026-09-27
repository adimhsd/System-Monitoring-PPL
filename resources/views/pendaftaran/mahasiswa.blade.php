@extends('layouts.publik')

@section('title', 'Pendaftaran Mahasiswa PPL')
@section('heading', 'Formulir Pendaftaran Mahasiswa PPL')
@section('subheading', 'PPL Reguler & Rekognisi PPL MBKM — Fakultas Ekonomi dan Bisnis')

@section('content')
@if(! $dibuka)
    <div class="alert alert-warning text-center mb-0">
        <strong>Pendaftaran mahasiswa PPL sedang ditutup.</strong><br>
        <span class="fs-7">Silakan hubungi Admin PPL FEB UNIKU untuk informasi lebih lanjut.</span>
    </div>
@else
    @if($errors->any())
        <div class="alert alert-danger fs-7">
            <strong>Periksa kembali isian Anda:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('pendaftaran.mahasiswa.simpan') }}" method="POST" enctype="multipart/form-data"
          x-data="{ jenis: '{{ old('jenis_ppl', '') }}' }">
        @csrf

        <div class="form-section-title mt-0">1. Jenis PPL</div>
        <div class="row g-3">
            <div class="col-12 col-sm-6">
                <input type="radio" class="btn-check" name="jenis_ppl" id="jenisReguler" value="reguler" x-model="jenis" @checked(old('jenis_ppl') === 'reguler') required>
                <label class="pilihan-card border rounded-4 p-3 w-100 h-100" for="jenisReguler">
                    <div class="fw-bold">🏢 PPL Reguler</div>
                    <div class="text-muted fs-8">Ditempatkan di instansi mitra yang ditentukan Fakultas.</div>
                </label>
            </div>
            <div class="col-12 col-sm-6">
                <input type="radio" class="btn-check" name="jenis_ppl" id="jenisMbkm" value="mbkm" x-model="jenis" @checked(old('jenis_ppl') === 'mbkm')>
                <label class="pilihan-card border rounded-4 p-3 w-100 h-100" for="jenisMbkm">
                    <div class="fw-bold">🎓 Rekognisi PPL MBKM</div>
                    <div class="text-muted fs-8">Sedang/akan mengikuti program MBKM di luar kampus.</div>
                </label>
            </div>
        </div>

        <div x-show="jenis === 'mbkm'" x-cloak>
            <div class="form-section-title">Data Program MBKM</div>
            <div class="row g-3">
                <div class="col-12 col-sm-6">
                    <label for="program_mbkm" class="form-label fw-semibold fs-7">Program MBKM <span class="text-danger">*</span></label>
                    <select id="program_mbkm" name="program_mbkm" class="form-select @error('program_mbkm') is-invalid @enderror" :required="jenis === 'mbkm'">
                        <option value="">-- Pilih Program --</option>
                        @foreach($programMbkm as $program)
                            <option value="{{ $program }}" @selected(old('program_mbkm') === $program)>{{ $program }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6">
                    <label for="instansi_mbkm" class="form-label fw-semibold fs-7">Instansi / Mitra MBKM <span class="text-danger">*</span></label>
                    <input type="text" id="instansi_mbkm" name="instansi_mbkm" maxlength="150" class="form-control @error('instansi_mbkm') is-invalid @enderror" value="{{ old('instansi_mbkm') }}" placeholder="Contoh: PT Bank Rakyat Indonesia" :required="jenis === 'mbkm'">
                </div>
            </div>
        </div>

        <div class="form-section-title">2. Data Diri Mahasiswa</div>
        <div class="row g-3">
            <div class="col-12 col-sm-5">
                <label for="nim" class="form-label fw-semibold fs-7">NIM <span class="text-danger">*</span></label>
                <input type="text" inputmode="numeric" id="nim" name="nim" maxlength="20" class="form-control @error('nim') is-invalid @enderror" value="{{ old('nim') }}" placeholder="Contoh: 20230510001" required>
            </div>
            <div class="col-12 col-sm-7">
                <label for="nama" class="form-label fw-semibold fs-7">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" id="nama" name="nama" maxlength="100" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" placeholder="Sesuai data SIAKAD" required>
            </div>
            <div class="col-12 col-sm-4">
                <label for="jenis_kelamin" class="form-label fw-semibold fs-7">Jenis Kelamin <span class="text-danger">*</span></label>
                <select id="jenis_kelamin" name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
                    <option value="">-- Pilih --</option>
                    @foreach(['Laki-laki', 'Perempuan'] as $jk)
                        <option value="{{ $jk }}" @selected(old('jenis_kelamin') === $jk)>{{ $jk }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-sm-4">
                <label for="prodi" class="form-label fw-semibold fs-7">Program Studi <span class="text-danger">*</span></label>
                <select id="prodi" name="prodi" class="form-select @error('prodi') is-invalid @enderror" required>
                    <option value="">-- Pilih Prodi --</option>
                    @foreach(['Manajemen' => 'S1 Manajemen', 'Akuntansi' => 'S1 Akuntansi', 'Bisnis Digital' => 'S1 Bisnis Digital'] as $val => $label)
                        <option value="{{ $val }}" @selected(old('prodi') === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-sm-4">
                <label for="konsentrasi" class="form-label fw-semibold fs-7">Konsentrasi</label>
                <select id="konsentrasi" name="konsentrasi" class="form-select @error('konsentrasi') is-invalid @enderror">
                    <option value="">-- Pilih Konsentrasi --</option>
                    @foreach($konsentrasiList as $k)
                        <option value="{{ $k }}" @selected(old('konsentrasi') === $k)>{{ $k }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-sm-6">
                <label for="no_hp" class="form-label fw-semibold fs-7">No. HP / WhatsApp <span class="text-danger">*</span></label>
                <input type="tel" id="no_hp" name="no_hp" maxlength="20" class="form-control @error('no_hp') is-invalid @enderror" value="{{ old('no_hp') }}" placeholder="Contoh: 081234567890" required>
            </div>
            <div class="col-12 col-sm-6">
                <label for="email" class="form-label fw-semibold fs-7">Email</label>
                <input type="email" id="email" name="email" maxlength="100" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="nama@gmail.com">
            </div>
            <div class="col-12">
                <label for="alamat" class="form-label fw-semibold fs-7">Alamat Domisili</label>
                <textarea id="alamat" name="alamat" rows="2" maxlength="500" class="form-control @error('alamat') is-invalid @enderror" placeholder="Alamat tempat tinggal selama PPL">{{ old('alamat') }}</textarea>
            </div>
        </div>

        <div class="form-section-title">3. Bukti Pembayaran</div>
        <label for="bukti_pembayaran" class="form-label fw-semibold fs-7">Unggah Bukti Pembayaran PPL (PDF, maks. 1 MB) <span class="text-danger">*</span></label>
        <input type="file" id="bukti_pembayaran" name="bukti_pembayaran" accept="application/pdf,.pdf" data-max-kb="1024"
               class="form-control file-pdf @error('bukti_pembayaran') is-invalid @enderror" required>
        <div class="form-text fs-8">Scan / simpan bukti pembayaran sebagai satu file PDF. Ukuran file tidak boleh lebih dari 1 MB.</div>
        <div class="invalid-feedback d-block fs-8 file-error"></div>

        <div class="form-check mt-4">
            <input class="form-check-input @error('pernyataan') is-invalid @enderror" type="checkbox" value="1" id="pernyataan" name="pernyataan" @checked(old('pernyataan')) required>
            <label class="form-check-label fs-7" for="pernyataan">
                Saya menyatakan bahwa data yang saya isikan adalah benar dan dapat dipertanggungjawabkan.
            </label>
        </div>

        <button type="submit" class="btn btn-primary btn-primary-custom text-white w-100 mt-4">📨 Kirim Pendaftaran</button>
        <div class="text-center mt-3 fs-7">
            <a href="{{ route('pendaftaran.index') }}" class="text-decoration-none text-secondary">&larr; Kembali</a>
        </div>
    </form>
@endif
@endsection

@include('pendaftaran._cek-file')
