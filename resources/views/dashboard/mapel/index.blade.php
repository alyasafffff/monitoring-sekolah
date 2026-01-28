@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold">Data Mata Pelajaran</h2>
        <a href="{{ route('mapel.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Tambah Mapel
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow border-0">
        <div class="card-body">
            <table class="table table-hover table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">Kode Mapel</th>
                        <th>Nama Mata Pelajaran</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mapel as $index => $m)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><span class="badge bg-info text-dark">{{ $m->kode_mapel }}</span></td>
                        <td class="fw-bold">{{ $m->nama_mapel }}</td>
                        <td class="text-center">
                            <a href="{{ route('mapel.edit', $m->id) }}" class="btn btn-sm btn-warning me-1"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('mapel.destroy', $m->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus mapel ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted">Belum ada data mata pelajaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection