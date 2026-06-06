@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0">
                <div class="card-header bg-white fw-bold">Tambah Mata Pelajaran</div>
                <div class="card-body">
                    <form action="{{ route('mapel.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Kode Mapel (Singkatan)</label>
                            <input type="text"
                                name="kode_mapel"
                                class="form-control @error('kode_mapel') is-invalid @enderror"
                                value="{{ old('kode_mapel') }}"
                                placeholder="Contoh: MTK, IND, IPA"
                                required
                                maxlength="10">
                            <small class="text-muted">Maksimal 10 karakter.</small>
                            @error('kode_mapel')
                            <div class="invalid-feedback">Kode Mapel sudah terdaftar! Gunakan kode lain.</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Mata Pelajaran</label>
                            <input type="text"
                                name="nama_mapel"
                                class="form-control @error('nama_mapel') is-invalid @enderror"
                                value="{{ old('nama_mapel') }}"
                                placeholder="Contoh: Matematika"
                                required>
                            @error('nama_mapel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('mapel.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection