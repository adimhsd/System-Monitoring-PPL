@extends('layouts.app')

@section('title', 'Edit Data Mahasiswa')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.mahasiswa.index') }}" class="text-decoration-none text-secondary fs-7">&larr; Kembali ke Master Data Mahasiswa</a>
    <h4 class="fw-bold mb-1 mt-1">Edit Data Mahasiswa — {{ $mahasiswa->nama }}</h4>
    <p class="text-muted mb-0 fs-7">Perbarui data mahasiswa atau ubah penempatan kelompok PPL.</p>
</div>

<div class="row g-4 max-w-2xl mx-auto">
    <div class="col-12">
        <div class="card card-custom p-4">
            <form action="{{ route('admin.mahasiswa.update', $mahasiswa) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3 mb-3">
                    <div class="col-12 col-sm-6">
                        <label for="nim" class="form-label fw-semibold text-secondary fs-7">NIM (Nomor Induk Mahasiswa) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nim') is-invalid @enderror" id="nim" name="nim" value="{{ old('nim', $mahasiswa->nim) }}" required>
                        @error('nim')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6">
                        <label for="nama" class="form-label fw-semibold text-secondary fs-7">Nama Lengkap Mahasiswa <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $mahasiswa->nama) }}" required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-sm-6">
                        <label for="jenis_kelamin" class="form-label fw-semibold text-secondary fs-7">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select class="form-select @error('jenis_kelamin') is-invalid @enderror" id="jenis_kelamin" name="jenis_kelamin" required>
                            <option value="Laki-laki" {{ old('jenis_kelamin', $mahasiswa->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('jenis_kelamin', $mahasiswa->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6">
                        <label for="prodi" class="form-label fw-semibold text-secondary fs-7">Program Studi <span class="text-danger">*</span></label>
                        <select class="form-select @error('prodi') is-invalid @enderror" id="prodi" name="prodi" required>
                            <option value="Manajemen" {{ old('prodi', $mahasiswa->prodi) == 'Manajemen' ? 'selected' : '' }}>Manajemen</option>
                            <option value="Akuntansi" {{ old('prodi', $mahasiswa->prodi) == 'Akuntansi' ? 'selected' : '' }}>Akuntansi</option>
                            <option value="Bisnis Digital" {{ old('prodi', $mahasiswa->prodi) == 'Bisnis Digital' ? 'selected' : '' }}>Bisnis Digital</option>
                        </select>
                        @error('prodi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-sm-6">
                        <label for="konsentrasi" class="form-label fw-semibold text-secondary fs-7">Konsentrasi / Peminatan</label>
                        <select class="form-select @error('konsentrasi') is-invalid @enderror" id="konsentrasi" name="konsentrasi">
                            <option value="">-- Pilih Konsentrasi --</option>
                            <option value="Pemasaran" {{ old('konsentrasi', $mahasiswa->konsentrasi) == 'Pemasaran' ? 'selected' : '' }}>Pemasaran</option>
                            <option value="Operasional" {{ old('konsentrasi', $mahasiswa->konsentrasi) == 'Operasional' ? 'selected' : '' }}>Operasional</option>
                            <option value="Keuangan" {{ old('konsentrasi', $mahasiswa->konsentrasi) == 'Keuangan' ? 'selected' : '' }}>Keuangan</option>
                            <option value="SDM" {{ old('konsentrasi', $mahasiswa->konsentrasi) == 'SDM' ? 'selected' : '' }}>SDM</option>
                            <option value="Akuntansi" {{ old('konsentrasi', $mahasiswa->konsentrasi) == 'Akuntansi' ? 'selected' : '' }}>Akuntansi</option>
                            <option value="Bisnis Digital" {{ old('konsentrasi', $mahasiswa->konsentrasi) == 'Bisnis Digital' ? 'selected' : '' }}>Bisnis Digital</option>
                        </select>
                        @error('konsentrasi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-sm-6">
                        <label for="no_hp" class="form-label fw-semibold text-secondary fs-7">No. HP / Whatsapp</label>
                        <input type="text" class="form-control @error('no_hp') is-invalid @enderror" id="no_hp" name="no_hp" value="{{ old('no_hp', $mahasiswa->no_hp) }}">
                        @error('no_hp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="kelompok_id" class="form-label fw-semibold text-secondary fs-7">Hubungkan ke Kelompok PPL</label>
                    <select class="form-select @error('kelompok_id') is-invalid @enderror" id="kelompok_id" name="kelompok_id">
                        <option value="">-- Belum Ada Kelompok --</option>
                        @foreach($kelompokList as $k)
                            <option value="{{ $k->id }}" {{ old('kelompok_id', $mahasiswa->kelompok_id) == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kelompok }} ({{ $k->mitra->nama_mitra ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                    @error('kelompok_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="alamat" class="form-label fw-semibold text-secondary fs-7">Alamat Tempat Tinggal</label>
                    <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3">{{ old('alamat', $mahasiswa->alamat) }}</textarea>
                    @error('alamat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary btn-touch text-white rounded-3 w-100 fw-semibold">
                    Simpan Perubahan Data Mahasiswa
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
