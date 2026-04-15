@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0">
                <div class="card-header bg-white fw-bold">Edit Kegiatan Sekolah</div>
                <div class="card-body">
                    <form action="{{ route('kegiatan.update', $kegiatan->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Kegiatan</label>
                            <input type="text" name="nama_kegiatan" class="form-control" value="{{ old('nama_kegiatan', $kegiatan->nama_kegiatan) }}" required>
                            @error('nama_kegiatan') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi (Opsional)</label>
                            <textarea name="deskripsi" class="form-control" rows="3" placeholder="Masukkan deskripsi kegiatan">{{ old('deskripsi', $kegiatan->deskripsi) }}</textarea>
                            @error('deskripsi') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('kegiatan.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-warning">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection