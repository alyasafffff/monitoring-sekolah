@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0">
                <div class="card-header bg-white fw-bold">Tambah Kegiatan Sekolah</div>
                <div class="card-body">
                    <form action="{{ route('kegiatan.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Kegiatan</label>
                            <input type="text" name="nama_kegiatan" class="form-control" placeholder="Contoh: Jumat Berseri, Upacara" required>
                            @error('nama_kegiatan') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi (Opsional)</label>
                            <textarea name="deskripsi" class="form-control" rows="3" placeholder="Masukkan deskripsi singkat kegiatan"></textarea>
                            @error('deskripsi') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('kegiatan.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection