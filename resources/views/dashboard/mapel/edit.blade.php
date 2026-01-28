@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0">
                <div class="card-header bg-white fw-bold">Edit Mata Pelajaran</div>
                <div class="card-body">
                    <form action="{{ route('mapel.update', $mapel->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Kode Mapel</label>
                            <input type="text" name="kode_mapel" class="form-control" value="{{ $mapel->kode_mapel }}" required maxlength="10">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Mata Pelajaran</label>
                            <input type="text" name="nama_mapel" class="form-control" value="{{ $mapel->nama_mapel }}" required>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('mapel.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-warning">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection