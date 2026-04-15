@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold">Data Kegiatan Sekolah</h2>
        <a href="{{ route('kegiatan.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Tambah Kegiatan
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
                        <th>Nama Kegiatan</th>
                        <th>Deskripsi</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kegiatan as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        {{-- Deskripsi bisa kita kasih badge atau style yang mirip kode mapel jika mau, 
                             tapi di sini saya biarkan nama_kegiatan yang bold agar selaras --}}
                        <td class="fw-bold">{{ $item->nama_kegiatan }}</td>
                        <td>{{ $item->deskripsi ?? '-' }}</td>
                        <td class="text-center">
                            <a href="{{ route('kegiatan.edit', $item->id) }}" class="btn btn-sm btn-warning me-1">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('kegiatan.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kegiatan ini?')">
                                @csrf 
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">Belum ada data kegiatan sekolah.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection