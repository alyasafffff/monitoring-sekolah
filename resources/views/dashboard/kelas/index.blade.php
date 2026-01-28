@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold">Manajemen Kelas</h2>
        <a href="{{ route('kelas.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Tambah Kelas
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow border-0">
        <div class="card-body">
            <table class="table table-hover table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="20%">Nama Kelas</th>
                        <th>Wali Kelas</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kelas as $index => $k)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="fw-bold text-primary">{{ $k->nama_kelas }}</td>
                        <td>
                            @if($k->nama_wali_kelas)
                                <i class="fas fa-user-tie me-1 text-muted"></i> {{ $k->nama_wali_kelas }}
                            @else
                                <span class="badge bg-danger">Belum Ada</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('kelas.edit', $k->id) }}" class="btn btn-sm btn-warning me-1">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('kelas.destroy', $k->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kelas {{ $k->nama_kelas }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">Belum ada data kelas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection