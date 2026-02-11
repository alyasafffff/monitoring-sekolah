@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold">Manajemen Akun Pengguna</h2>
        {{-- ROUTE YANG BENAR: users.create --}}
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Tambah User Baru
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    {{-- TABEL 1: PETINGGI --}}
    <div class="card shadow border-0 mb-4">
        <div class="card-header bg-dark text-white fw-bold">
            <i class="fas fa-user-tie me-2"></i> Daftar Manajemen & Staff
        </div>
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nama Lengkap</th>
                        <th>NIP / Username</th>
                        <th>Jabatan (Role)</th>
                        <th>No. HP</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($petinggi as $p)
                    <tr>
                        <td class="fw-bold">{{ $p->name }}</td>
                        <td>{{ $p->nip }}</td>
                        <td>
                            @if($p->role == 'admin') <span class="badge bg-danger">ADMINISTRATOR</span>
                            @elseif($p->role == 'kepsek') <span class="badge bg-primary">KEPALA SEKOLAH</span>
                            @elseif($p->role == 'bk') <span class="badge bg-warning text-dark">GURU BK</span>
                            @endif
                        </td>
                        <td>{{ $p->no_hp ?? '-' }}</td>
                        <td class="text-center">
                            {{-- PERBAIKAN: Gunakan users.edit --}}
                            <a href="{{ route('users.edit', $p->id) }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-edit"></i></a>
                            
                            @if(auth()->id() != $p->id)
                                {{-- PERBAIKAN: Gunakan users.destroy --}}
                                <form action="{{ route('users.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus user ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- TABEL 2: GURU --}}
    <div class="card shadow border-0">
        <div class="card-header bg-white fw-bold text-primary border-bottom">
            <i class="fas fa-chalkboard-teacher me-2"></i> Daftar Guru Pengajar
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>NIP</th>
                            <th>Nama Guru</th>
                            <th>Status Akun</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($guru as $index => $g)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $g->nip }}</td>
                            <td>{{ $g->name }}</td>
                            <td>
                                @if($g->is_active) <span class="badge bg-success">Aktif</span>
                                @else <span class="badge bg-secondary">Non-Aktif</span> @endif
                            </td>
                            <td class="text-center">
                                {{-- PERBAIKAN: Gunakan users.edit --}}
                                <a href="{{ route('users.edit', $g->id) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                
                                {{-- PERBAIKAN: Gunakan users.destroy --}}
                                <form action="{{ route('users.destroy', $g->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus guru ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted">Belum ada data guru.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection