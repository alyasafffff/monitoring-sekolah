@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold">Manajemen Data Siswa</h2>
        <a href="{{ route('siswa.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Tambah Siswa
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Info Siswa</th>
                            <th>Kelas</th>
                            <th>QR Code</th> <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($siswa as $index => $s)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold">{{ $s->nama_siswa }}</div>
                                <small class="text-muted">NISN: {{ $s->nisn }}</small>
                            </td>
                            <td><span class="badge bg-info text-dark">{{ $s->nama_kelas }}</span></td>
                            
                            <td class="text-center">
                                <div class="d-flex flex-column align-items-center">
                                    {!! QrCode::size(80)->generate($s->qr_token) !!}
                                    
                                    <small class="text-muted mt-1" style="font-size: 10px;">{{ $s->qr_token }}</small>
                                </div>
                            </td>

                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('siswa.edit', $s->id) }}" class="btn btn-sm btn-warning" title="Edit Data">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <a href="{{ route('siswa.cetak', $s->id) }}" target="_blank" class="btn btn-sm btn-info text-white" title="Cetak Kartu">
                                        <i class="fas fa-id-card"></i>
                                    </a>

                                    <form action="{{ route('siswa.destroy', $s->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus siswa ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection