@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0">
                <div class="card-header bg-white fw-bold">Edit Kelas</div>
                <div class="card-body">
                    <form action="{{ route('kelas.update', $kelas->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Kelas</label>
                            <input type="text" name="nama_kelas" class="form-control" value="{{ $kelas->nama_kelas }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Wali Kelas</label>
                            <select name="wali_kelas_id" class="form-select" required>
                                @foreach($guru as $g)
                                    <option value="{{ $g->id }}" {{ $kelas->wali_kelas_id == $g->id ? 'selected' : '' }}>
                                        {{ $g->name }} (NIP: {{ $g->nip }})
                                    </option>
                                @endforeach
                            </select>
                            @error('wali_kelas_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('kelas.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-warning">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection