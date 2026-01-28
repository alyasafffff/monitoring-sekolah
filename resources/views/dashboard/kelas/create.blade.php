@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0">
                <div class="card-header bg-white fw-bold">Tambah Kelas Baru</div>
                <div class="card-body">
                    <form action="{{ route('kelas.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Kelas</label>
                            <input type="text" name="nama_kelas" class="form-control" placeholder="Contoh: 7A" required>
                            @error('nama_kelas') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Wali Kelas</label>
                            <select name="wali_kelas_id" class="form-select" required>
                                <option value="">-- Pilih Guru --</option>
                                @foreach($guru as $g)
                                    <option value="{{ $g->id }}">{{ $g->name }} (NIP: {{ $g->nip }})</option>
                                @endforeach
                            </select>
                            @error('wali_kelas_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('kelas.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection