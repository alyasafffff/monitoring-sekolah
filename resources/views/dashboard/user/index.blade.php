@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-primary fw-bold mb-0">Manajemen Akun Pengguna</h2>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-user-plus me-1"></i> Tambah User Baru
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card shadow border-0 mb-4">
        <div class="card-body">
            <form action="{{ route('users.index') }}" method="GET">
                <div class="row g-2 align-items-center">
                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-primary text-primary">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" name="q" class="form-control border-primary"
                                placeholder="Cari berdasarkan Nama atau NIP" value="{{ request('q') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100 shadow-sm" type="submit">Cari Data</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow border-0 mb-5">
    <div class="card-header bg-dark text-white fw-bold py-3">
        <i class="fas fa-user-shield me-2 text-warning"></i> Daftar Manajemen & Staff
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4" width="5%">No</th>
                        <th width="8%">Profil</th>
                        <th>Nama Lengkap</th>
                        <th>NIP / Username</th>
                        <th>Jabatan (Role)</th>
                        <th>Status</th> {{-- Kolom Baru --}}
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($petinggi as $index => $p)
                    <tr>
                        <td class="px-4 text-muted">{{ $index + 1 }}</td>
                        <td>
                            <img src="{{ $p->foto_profil ? asset('storage/' . $p->foto_profil) : 'https://ui-avatars.com/api/?name='.urlencode($p->name).'&background=3b82f6&color=fff' }}"
                                 class="rounded-circle border shadow-sm"
                                 width="45" height="45"
                                 style="object-fit: cover;">
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $p->name }}</div>
                            <small class="text-muted">{{ $p->no_hp ?? '-' }}</small>
                        </td>
                        <td class="text-secondary fw-medium">{{ $p->nip }}</td>
                        <td>
                            @if($p->role == 'admin')
                                <span class="badge bg-primary-subtle text-primary border border-primary px-3 py-2" style="font-size: 10px;">ADMINISTRATOR</span>
                            @elseif($p->role == 'kepsek')
                                <span class="badge bg-info-subtle text-info border border-info px-3 py-2" style="font-size: 10px;">KEPALA SEKOLAH</span>
                            @elseif($p->role == 'bk')
                                <span class="badge bg-warning-subtle text-warning border border-warning px-3 py-2 text-dark" style="font-size: 10px;">GURU BK</span>
                            @endif
                        </td>
                        <td>
                            {{-- Logic Status Akun --}}
                            @if(isset($p->is_active) && $p->is_active)
                                <span class="text-success small fw-bold"><i class="fas fa-circle me-1" style="font-size: 8px;"></i> Aktif</span>
                            @else
                                <span class="text-secondary small fw-bold"><i class="fas fa-circle me-1" style="font-size: 8px;"></i> Non-Aktif</span>
                            @endif
                        </td>
                        <td class="text-center px-4">
                            <div class="btn-group">
                                <a href="{{ route('users.edit', $p->id) }}" class="btn btn-sm btn-outline-warning" title="Edit Akun"><i class="fas fa-edit"></i></a>

                                @if(auth()->id() != $p->id)
                                <form action="{{ route('users.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus user ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger ms-1" title="Hapus Akun"><i class="fas fa-trash"></i></button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted small">Data tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div> 

    <div class="card shadow border-0 mb-4">
    <div class="card-header bg-white fw-bold text-primary border-bottom py-3">
        <i class="fas fa-chalkboard-teacher me-2"></i> Daftar Guru Pengajar
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4 text-center" width="5%">No</th>
                        <th width="8%">Profil</th>
                        <th>Nama Lengkap</th>
                        <th>NIP</th>
                        <th>Status</th> {{-- Kolom disamakan --}}
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($guru as $index => $g)
                    <tr>
                        <td class="px-4 text-center text-muted">{{ $index + 1 }}</td>
                        <td>
                            <img src="{{ $g->foto_profil ? asset('storage/' . $g->foto_profil) : 'https://ui-avatars.com/api/?name='.urlencode($g->name).'&background=22c55e&color=fff' }}"
                                 class="rounded-circle border shadow-sm"
                                 width="40" height="40"
                                 style="object-fit: cover;">
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $g->name }}</div>
                            <small class="text-muted">{{ $g->no_hp ?? '-' }}</small>
                        </td>
                        <td class="text-secondary fw-medium">{{ $g->nip }}</td>
                        <td>
                            {{-- Style disamakan dengan dot indicator --}}
                            @if(isset($g->is_active) && $g->is_active)
                                <span class="text-success small fw-bold">
                                    <i class="fas fa-circle me-1" style="font-size: 8px;"></i> Aktif
                                </span>
                            @else
                                <span class="text-secondary small fw-bold">
                                    <i class="fas fa-circle me-1" style="font-size: 8px;"></i> Non-Aktif
                                </span>
                            @endif
                        </td>
                        <td class="text-center px-4">
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="{{ route('users.edit', $g->id) }}" class="btn btn-sm btn-outline-warning" title="Edit Guru">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('users.destroy', $g->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus guru ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger shadow-sm" title="Hapus Guru">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted small">Belum ada data guru.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
@endsection